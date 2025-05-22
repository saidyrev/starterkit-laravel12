<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('role')->select('users.*');
            
            // Apply filters
            if ($request->filled('role_filter')) {
                $users->where('role_id', $request->role_filter);
            }
            
            if ($request->filled('status_filter')) {
                if ($request->status_filter === 'active') {
                    $users->whereNotNull('email_verified_at');
                } else {
                    $users->whereNull('email_verified_at');
                }
            }
            
            if ($request->filled('date_range')) {
                $dateRange = explode(' to ', $request->date_range);
                if (count($dateRange) === 2) {
                    $users->whereBetween('created_at', [
                        Carbon::parse($dateRange[0])->startOfDay(),
                        Carbon::parse($dateRange[1])->endOfDay()
                    ]);
                }
            }
            
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('checkbox', function($user) {
                    if ($user->id !== auth()->id()) {
                        return '<input type="checkbox" class="user-checkbox" value="' . $user->id . '">';
                    }
                    return '';
                })
                // Update bagian avatar_name di DataTables response
                ->addColumn('avatar_name', function($user) {
                    $avatarUrl = $user->avatar ? Storage::url('avatars/' . $user->avatar) : asset('sneat/assets/img/avatars/1.png');
                    return '
                    <div class="user-info-cell">
                    <div class="user-avatar-container">
                        <img src="' . $avatarUrl . '" alt="' . $user->name . '" class="user-avatar">
                    </div>
                    <div class="user-details">
                        <div class="user-name">' . $user->name . '</div>
                        <div class="user-email" title="' . $user->email . '">' . $user->email . '</div>
                    </div>
                    </div>';
                })
                ->addColumn('role_badge', function($user) {
                    if ($user->role) {
                        $badgeClass = match($user->role->name) {
                            'admin' => 'bg-label-danger',
                            'editor' => 'bg-label-warning',
                            'manager' => 'bg-label-info',
                            default => 'bg-label-primary'
                        };
                        return '<span class="badge ' . $badgeClass . ' rounded-pill">' . $user->role->display_name . '</span>';
                    }
                    return '<span class="badge bg-label-secondary rounded-pill">No Role</span>';
                })
                ->addColumn('status_badge', function($user) {
                    $isActive = $user->email_verified_at ? true : false;
                    $statusClass = $isActive ? 'bg-label-success' : 'bg-label-warning';
                    $statusText = $isActive ? 'Active' : 'Pending';
                    $icon = $isActive ? 'bx-check-circle' : 'bx-time-five';
                    return '<span class="badge ' . $statusClass . ' rounded-pill">
                        <i class="bx ' . $icon . ' me-1"></i>' . $statusText . '
                    </span>';
                })
                ->addColumn('last_active', function($user) {
                    return '<div class="text-nowrap">
                        <small class="text-muted">
                            <i class="bx bx-time me-1"></i>
                            ' . $user->updated_at->diffForHumans() . '
                        </small>
                    </div>';
                })
                ->addColumn('action', function($user) {
                    $actions = '
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item btn-show" href="javascript:void(0)" data-id="' . $user->id . '">
                                <i class="bx bx-show me-1"></i> View Profile
                            </a>
                            <a class="dropdown-item btn-edit" href="javascript:void(0)" data-id="' . $user->id . '">
                                <i class="bx bx-edit-alt me-1"></i> Edit User
                            </a>';
                    
                    if ($user->id !== auth()->id()) {
                        $statusAction = $user->email_verified_at ? 'Deactivate' : 'Activate';
                        $statusIcon = $user->email_verified_at ? 'bx-user-x' : 'bx-user-check';
                        
                        $actions .= '
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item btn-toggle-status" href="javascript:void(0)" 
                               data-id="' . $user->id . '" data-status="' . ($user->email_verified_at ? 'active' : 'inactive') . '">
                                <i class="bx ' . $statusIcon . ' me-1"></i> ' . $statusAction . '
                            </a>
                            <a class="dropdown-item btn-reset-password" href="javascript:void(0)" data-id="' . $user->id . '">
                                <i class="bx bx-reset me-1"></i> Reset Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger btn-delete" href="javascript:void(0)" 
                               data-id="' . $user->id . '" data-name="' . $user->name . '">
                                <i class="bx bx-trash me-1"></i> Delete User
                            </a>';
                    }
                    
                    $actions .= '</div></div>';
                    
                    return $actions;
                })
                ->rawColumns(['checkbox', 'avatar_name', 'role_badge', 'status_badge', 'last_active', 'action'])
                ->make(true);
        }

        $roles = Role::all();
        $stats = [
            'total' => User::count(),
            'active' => User::whereNotNull('email_verified_at')->count(),
            'pending' => User::whereNull('email_verified_at')->count(),
            'this_month' => User::whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        return view('users.index', compact('roles', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'send_welcome_email' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'email_verified_at' => $request->send_welcome_email ? null : now(),
        ]);

        // Here you can add email sending logic if needed

        return response()->json([
            'success' => true,
            'message' => 'User created successfully!'
        ]);
    }

    public function show(User $user)
    {
        $user->load(['role.permissions']);
        
        // Add some stats for the user
        $userStats = [
            'days_since_created' => $user->created_at->diffInDays(now()),
            'last_login' => $user->updated_at->diffForHumans(),
            'permissions_count' => $user->role ? $user->role->permissions->count() : 0
        ];

        return response()->json([
            'success' => true,
            'data' => $user,
            'stats' => $userStats
        ]);
    }

    public function edit(User $user)
    {
        $user->load('role');
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!'
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 400);
        }

        // Delete avatar if exists
        if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
            Storage::delete('public/avatars/' . $user->avatar);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!'
        ]);
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own status.'
            ], 400);
        }

        $newStatus = $user->email_verified_at ? null : now();
        $user->update(['email_verified_at' => $newStatus]);

        $statusText = $newStatus ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "User {$statusText} successfully!"
        ]);
    }

    public function resetPassword(User $user)
    {
        $newPassword = 'password123'; // You can generate random password
        $user->update(['password' => Hash::make($newPassword)]);

        return response()->json([
            'success' => true,
            'message' => "Password reset successfully! New password: {$newPassword}",
            'new_password' => $newPassword
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $currentUserId = auth()->id();
        $userIds = array_filter($request->user_ids, function($id) use ($currentUserId) {
            return $id != $currentUserId;
        });

        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid users selected.'
            ], 400);
        }

        $count = 0;
        switch ($request->action) {
            case 'delete':
                $count = User::whereIn('id', $userIds)->count();
                User::whereIn('id', $userIds)->delete();
                $message = "{$count} users deleted successfully!";
                break;
                
            case 'activate':
                $count = User::whereIn('id', $userIds)->update(['email_verified_at' => now()]);
                $message = "{$count} users activated successfully!";
                break;
                
            case 'deactivate':
                $count = User::whereIn('id', $userIds)->update(['email_verified_at' => null]);
                $message = "{$count} users deactivated successfully!";
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function export(Request $request)
    {
        $users = User::with('role');
        
        // Apply same filters as index
        if ($request->filled('role_filter')) {
            $users->where('role_id', $request->role_filter);
        }
        
        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'active') {
                $users->whereNotNull('email_verified_at');
            } else {
                $users->whereNull('email_verified_at');
            }
        }

        $users = $users->get();
        
        $csvData = [];
        $csvData[] = ['ID', 'Name', 'Email', 'Role', 'Status', 'Created At', 'Last Updated'];
        
        foreach ($users as $user) {
            $csvData[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->role ? $user->role->display_name : 'No Role',
                $user->email_verified_at ? 'Active' : 'Pending',
                $user->created_at->format('Y-m-d H:i:s'),
                $user->updated_at->format('Y-m-d H:i:s')
            ];
        }

        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $handle = fopen('php://temp', 'w+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}