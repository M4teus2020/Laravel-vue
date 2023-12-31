<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function index() {
        $searchQuery = request('query');
        $users = User::query()
            ->when(request('query'), function ($query, $searchQuery) {
                return $query->where('name', 'like', '%'. $searchQuery. '%');
            })
            ->paginate();

        return response()->json($users);
    }

    public function store() {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8'
        ]);

        return User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'role' => 2
        ]);
    }

    public function update(User $user) {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:8'
        ]);

        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ? bcrypt(request('password')) : $user->password,
        ]);
        return $user;
    }

    public function destroy(User $user) {
        $user->delete();
        return response()->noContent();
    }

    public function ChangeRole(User $user) {
        $user->update([
            'role' => request('role')
        ]);
        return response()->json(['success' => true]);
    }

    public function bulkDelete() {
        $users = User::whereIn('id', request('ids'))->delete();
        return response()->json(['mensage' => 'Users deleted successfully!']);
    }
}
