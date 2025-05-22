<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Helpers\SweetAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('role')->select('users.*');
            
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('role_name', function($user) {
                    if ($user->role) {
                        $badgeClass = match($user->role->name) {
                            'admin' => 'bg-label-danger',
                            'editor' => 'bg-label-warning',
                            default => 'bg-label-info'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $user->role->display_name . '</span>';
                    }
                    return '<span class="badge bg-label-secondary">No Role</span>';
                })
                ->addColumn('avatar', function($user) {
                    return '<div class="avatar avatar-sm me-3">
                        <img src="' . asset('sneat/assets/img/avatars/1.png') . '" alt="Avatar" class="rounded-circle">
                    </div>';
                })
                ->addColumn('created_formatted', function($user) {
                    return $user->created_at->format('M d, Y');
                })
                ->addColumn('action', function($user) {
                    $actions = '<div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn-show" href="javascript:void(0)" data-id="' . $user->id . '">
                                <i class="bx bx-show me-1"></i> View
                            </a>
                            <a class="dropdown-item btn-edit" href="javascript:void(0)" data-id="' . $user->id . '">
                                <i class="bx bx-edit-alt me-1"></i> Edit
                            </a>';
                    
                    if ($user->id !== auth()->id()) {
                        $actions .= '<a class="dropdown-item text-danger btn-delete" href="javascript:void(0)" data-id="' . $user->id . '" data-name="' . $user->name . '">
                                <i class="bx bx-trash me-1"></i> Delete
                            </a>';
                    }
                    
                    $actions .= '</div></div>';
                    
                    return $actions;
                })
                ->rawColumns(['role_name', 'avatar', 'action'])
                ->make(true);
        }

        $roles = Role::all();
        return view('users.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully!'
        ]);
    }

    public function show(User $user)
    {
        $user->load('role');
        return response()->json([
            'success' => true,
            'data' => $user
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

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!'
        ]);
    }
}