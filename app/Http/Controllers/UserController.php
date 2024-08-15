<?php

namespace App\Http\Controllers;

use App\enums\PrefixName;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    /**
     * Display a listing of the soft deleted resource.
     */
    public function trashed()
    {
        $users = User::onlyTrashed()->orderBy('deleted_at','desc')->paginate(10);
        return view('pages.users.trashed', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prefixname' => ['required', Rule::in(array_column(PrefixName::cases(), 'value'))],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }

        $user = User::create($validated);

        return redirect()->route('users.index')->with([
            'title' => 'User Created',
            'message' => 'The user has been created successfully.',
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('pages.users.show' , compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('pages.users.update' , compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'prefixname' => ['required', Rule::in(array_column(PrefixName::cases(), 'value'))],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255',  Rule::unique(User::class)->ignore($user->id)],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'string', 'confirmed',  Password::defaults()],
        ]);

        if ($request->has('password')) {
            $validated['password'] = Hash::make($validated['password']);
        }else{
            unset($validated['password']);
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }

        $user->update($validated);

        return redirect()->route('users.index')->with([
            'title' => 'User Updated',
            'message' => 'The user has been updated successfully.',
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            $user->delete();
            return redirect()->route('users.index')->with([
                'title' => 'User Deleted',
                'message' => 'The user has been deleted successfully.',
                'success' => true,
            ]);
        }
    }

    public function restore(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user) {
            $user->restore();
            return redirect()->back()->with([
                'title' => 'User Restored',
                'message' => 'The user has been restored successfully.',
                'success' => true,
            ]);
        }
    }

    public function delete(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->forceDelete();
            return redirect()->back()->with([
                'title' => 'User Deleted Permanently',
                'message' => 'The user has been deleted successfully.',
                'success' => true,
            ]);
        }
    }
}
