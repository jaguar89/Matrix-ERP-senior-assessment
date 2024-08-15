<?php

namespace App\Http\Controllers;

use App\enums\PrefixName;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * The UserService instance.
     *
     * @var \App\Services\UserServiceInterface
     */
    protected $userService;

    /**
     * Create a new instance of the controller.
     *
     * @param \App\Services\UserServiceInterface $userService
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // level-1
//        $users = User::latest()->paginate(10);

        // level-2
        $users = $this->userService->list();

        return view('pages.users.index', compact('users'));
    }

    /**
     * Display a listing of the soft deleted resource.
     */
    public function trashed()
    {
        //level-1
//        $users = User::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);

        //level-2
        $users = $this->userService->listTrashed();

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
    public function store(UserRequest $request)
    {
        // level-1
//        $validated = $request->validate([
//            'prefixname' => ['required', Rule::in(array_column(PrefixName::cases(), 'value'))],
//            'firstname' => ['required', 'string', 'max:255'],
//            'middlename' => ['required', 'string', 'max:255'],
//            'lastname' => ['required', 'string', 'max:255'],
//            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
//            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
//            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
//            'password' => ['required', 'string', 'confirmed', Password::defaults()],
//        ]);
//
//        $validated['password'] = Hash::make($validated['password']);
//
//        if ($request->hasFile('photo')) {
//            $validated['photo'] = $request->file('photo')->store('users', 'public');
//        }
//
//        $user = User::create($validated);

        // level-2
        $this->userService->store($request->except('password_confirmation'));

        return redirect()->route('users.index')->with([
            'title' => 'User Created',
            'message' => 'The user has been created successfully.',
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = $this->userService->find($id);
        return view('pages.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('pages.users.update', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, $id)
    {
        // level-1
//        $validated = $request->validate([
//            'prefixname' => ['required', Rule::in(array_column(PrefixName::cases(), 'value'))],
//            'firstname' => ['required', 'string', 'max:255'],
//            'middlename' => ['required', 'string', 'max:255'],
//            'lastname' => ['required', 'string', 'max:255'],
//            'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
//            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
//            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
//            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
//        ]);
//
//        if ($request->has('password')) {
//            $validated['password'] = Hash::make($validated['password']);
//        } else {
//            unset($validated['password']);
//        }
//
//        if ($request->hasFile('photo')) {
//            $validated['photo'] = $request->file('photo')->store('users', 'public');
//        }
//
//        $user->update($validated);

        // level-2
        $this->userService->update($id, $request->except('password_confirmation'));

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
        // level-1
//        $user = User::findOrFail($id);
//
//        if ($user) {
//            $user->delete();
//            return redirect()->route('users.index')->with([
//                'title' => 'User Deleted',
//                'message' => 'The user has been deleted successfully.',
//                'success' => true,
//            ]);
//        }

        // level-2
        $this->userService->destroy($id);
        return redirect()->route('users.index')->with([
            'title' => 'User Deleted',
            'message' => 'The user has been deleted successfully.',
            'success' => true,
        ]);
    }

    public function restore(string $id)
    {
        // level-1
//        $user = User::withTrashed()->findOrFail($id);
//
//        if ($user) {
//            $user->restore();
//            return redirect()->back()->with([
//                'title' => 'User Restored',
//                'message' => 'The user has been restored successfully.',
//                'success' => true,
//            ]);
//        }

        // level-2
        $this->userService->restore($id);
        return redirect()->back()->with([
            'title' => 'User Restored',
            'message' => 'The user has been restored successfully.',
            'success' => true,
        ]);
    }

    public function delete(string $id)
    {
        // level-1
//        $user = User::withTrashed()->findOrFail($id);
//
//        if ($user) {
//            if ($user->photo) {
//                Storage::disk('public')->delete($user->photo);
//            }
//
//            $user->forceDelete();
//            return redirect()->back()->with([
//                'title' => 'User Deleted Permanently',
//                'message' => 'The user has been deleted successfully.',
//                'success' => true,
//            ]);
//        }

        // level-2
        $this->userService->delete($id);
        return redirect()->back()->with([
            'title' => 'User Deleted Permanently',
            'message' => 'The user has been deleted successfully.',
            'success' => true,
        ]);
    }
}
