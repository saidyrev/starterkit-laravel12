<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Statistics
        $stats = [
            'total_users' => User::count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
        ];

        // User growth data for charts (last 12 months)
        $userGrowth = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Users by role for pie chart
        $usersByRole = Role::withCount('users')
            ->having('users_count', '>', 0)
            ->get()
            ->map(function ($role) {
                return [
                    'label' => $role->display_name,
                    'count' => $role->users_count,
                    'color' => $this->getRoleColor($role->name)
                ];
            });

        // Recent users (last 5)
        $recentUsers = User::with('role')
            ->latest()
            ->take(5)
            ->get();

        // Recent activity (you can expand this based on your needs)
        $activities = [
            [
                'user' => $user->name,
                'action' => 'Accessed dashboard',
                'time' => now()->diffForHumans(),
                'icon' => 'bx-home-circle',
                'color' => 'primary'
            ],
            // You can add more activities from logs or user actions
        ];

        return view('dashboard', compact(
            'stats', 
            'userGrowth', 
            'usersByRole', 
            'recentUsers', 
            'activities'
        ));
    }

    private function getRoleColor($roleName)
    {
        $colors = [
            'admin' => '#ff3e1d',
            'editor' => '#ffab00',
            'user' => '#28c76f',
            'manager' => '#7367f0',
        ];

        return $colors[$roleName] ?? '#8a8d93';
    }
}