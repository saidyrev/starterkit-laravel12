<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('role')->get();
            
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('role_name', function($user) {
                    if ($user->role) {
                        return '<span class="badge bg-primary">' . $user->role->display_name . '</span>';
                    }
                    return '<span class="badge bg-secondary">No Role</span>';
                })
                ->addColumn('created_formatted', function($user) {
                    return $user->created_at->format('M d, Y');
                })
                ->addColumn('action', function($user) {
                    $actions = '<div class="btn-group" role="group">';
                    
                    // Show button
                    $actions .= '<button type="button" class="btn btn-sm btn-outline-info btn-show" data-id="' . $user->id . '" title="View">
                        <i class="bx bx-show"></i>
                    </button>';
                    
                    // Edit button
                    $actions .= '<button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="' . $user->id . '" title="Edit">
                        <i class="bx bx-edit"></i>
                    </button>';
                    
                    // Delete button (tidak untuk user sendiri)
                    if ($user->id !== auth()->id()) {
                        $actions .= '<button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="' . $user->id . '" data-name="' . $user->name . '" title="Delete">
                            <i class="bx bx-trash"></i>
                        </button>';
                    }
                    
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['role_name', 'action'])
                ->make(true);
        }

        $roles = Role::all();
        return view('users.index', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role');
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('role');
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Cegah user menghapus dirinya sendiri
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