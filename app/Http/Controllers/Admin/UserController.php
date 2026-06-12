<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        $users = User::query()
            ->where('id', '!=', $authUser->id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('screens.admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(404);
        }

        $user->loadCount('orders');
        $user->load(['addresses' => fn ($query) => $query->orderByDesc('is_default')->latest()]);

        return view('screens.admin.users.show', compact('user'));
    }

    public function create()
    {
        return view('screens.admin.users.create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'profile_img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);
        

        $profile_Image = null;
        if ($request->hasFile('profile_img')) {
            $profile_Image = $request->file('profile_img')->store('profiles', 'public');
        }
        $validated['profile_img'] = $profile_Image;
        $user = User::create($validated);

        $user->assignRole('user');
        // return redirect()->route('users.index')->with('success', 'User created successfully');
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'redirect' => route('users.index'),
        ]);
    }
}
