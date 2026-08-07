<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSuperadmin();

        $users = User::query()
            ->withCount('mockTestHistories')
            ->withCount(['mockTestHistories as passed_mock_tests_count' => fn ($query) => $query->where('passed', true)])
            ->withMax('mockTestHistories', 'created_at')
            ->when($request->q, function ($query, $term) {
                $query->where(function ($searchQuery) use ($term) {
                    $searchQuery->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone_number', 'like', "%{$term}%")
                        ->orWhere('country', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $this->authorizeSuperadmin();

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSuperadmin();
        $user->update($request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', Rule::unique('users')->ignore($user)], 'phone_number' => ['nullable', 'string', 'max:30'], 'country' => ['nullable', 'string', 'max:100'], 'city' => ['nullable', 'string', 'max:100'], 'address' => ['nullable', 'string', 'max:500'], 'account_type' => ['required', Rule::in([User::ACCOUNT_TYPE_USER, User::ACCOUNT_TYPE_INSTRUCTOR])]]));

        return back()->with('success', 'User account updated.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->authorizeSuperadmin();
        $data = $request->validate(['password' => ['required', 'confirmed', Password::min(8)]]);
        $user->update(['password' => $data['password']]);

        return back()->with('success', 'Password reset successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorizeSuperadmin();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User account deleted.');
    }

    private function authorizeSuperadmin(): void
    {
        abort_unless(auth('admin')->user()?->is_superadmin, 403);
    }
}
