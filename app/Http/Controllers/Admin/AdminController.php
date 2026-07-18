<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $sections = Section::withCount('subSections')->get();
        $userStats = [
            'total' => User::count(),
            'users' => User::where('account_type', User::ACCOUNT_TYPE_USER)->count(),
            'instructors' => User::where('account_type', User::ACCOUNT_TYPE_INSTRUCTOR)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
        ];
        $recentUsers = User::latest()->limit(8)->get();

        return view('admin.home', compact('sections', 'userStats', 'recentUsers'));
    }
}
