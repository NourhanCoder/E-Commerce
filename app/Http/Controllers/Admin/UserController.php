<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('id', 'DESC')->paginate(5);
        return view('admin.dashboard', compact('users'));
    }

    public function toggle(User $user)
    {
        // نقلب قيمة العمود "active" (لو كانت 1 تبقى 0، ولو كانت 0 تبقى 1)
        $user->active = !$user->active;
        $user->save();

        return redirect()->route('admin.panel')->with('success', 'User status updated successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'type' => 'required|in:admin,customer', 
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable',
            'image',
            'mimes:png,jpg,jpeg,gif,webp'
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('users', $imageName, 'public');
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'type'     => $request->type,
            'password' => Hash::make($request->password),
            'active'   => $request->has('active'), // checkbox value
            'image' => $imageName,
        ]);

        return redirect()->route('admin.panel')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'type' => 'required|in:admin,customer',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->type = $request->type;

        // لو المستخدم رفع صورة جديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة 
            if ($user->image && Storage::disk('public')->exists('users/' . $user->image)) {
                Storage::disk('public')->delete('users/' . $user->image);
            }

            // حفظ الصورة الجديدة
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('users', $imageName, 'public');
            $user->image = $imageName;
        }

        $user->save();

        // $user->update($request->only('name', 'email', 'type'));

        return redirect()->route('admin.panel')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // حذف الصورة من مجلد التخزين (لو موجودة)
        if ($user->image && Storage::disk('public')->exists('users/' . $user->image)) {
            Storage::disk('public')->delete('users/' . $user->image);
        }
        $user->delete();

        return redirect()->route('admin.panel')->with('success', 'User deleted successfully.');
    }
}
