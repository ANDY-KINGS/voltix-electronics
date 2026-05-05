<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
// use App\Jobs\SendWelcomeEmail;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->paginate(15);
        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        return view('pages.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        $user = User::create($data);
        $user->assignRole($data['role']);

        // Queue Welcome Email (will be implemented in Phase 9)
        // SendWelcomeEmail::dispatch($user, $request->password);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('pages.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id() || $user->hasRole('admin')) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete yourself or another admin.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id() || $user->hasRole('admin')) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot deactivate yourself or another admin.');
        }

        $user->update(['is_active' => !$user->is_active]);
        return redirect()->route('admin.users.index')->with('success', 'User status updated successfully.');
    }
}
