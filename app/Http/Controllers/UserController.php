<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function register()
    {
        return view("user.register");
    }
    public function login()
    {
        return view("user.login");
    }

    public function loginFunction(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5|max:12'
        ]);

        $userInfo = User::where('email', $request->email)->first();

        if (!$userInfo) {
            return back()->withInput()->withErrors(['email' => 'Email not found']);
        }
        if ($userInfo->status === 'inactive') {
            return back()->withInput()->withErrors(['status' => 'Your account is inactive']);
        }

        if (!Hash::check($request->password, $userInfo->password)) {
            return back()->withInput()->withErrors(['password' => 'Incorrect password']);
        }

        session([
            'LoggedUserInfo' => $userInfo->id,
            'LoggedUserName' => $userInfo->name,
        ]);
        return redirect()->route('user.user_dashboard');
    }

    public function registerFucntion(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|regex:/^\S*$/',
        ], [
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.login')->with('success', 'User created successfully!');
    }


    public function dashboard()
    {
        $userId = session('LoggedUserInfo');

        if (!$userId) {
            return redirect('user/login')->with('fail', 'You must be logged in to access the dashboard');
        }

        $LoggedUserInfo = User::find($userId);

        $teachers = Teacher::all();

        return view('user.dashbaord', ['LoggedUserInfo' => $LoggedUserInfo, 'teachers' => $teachers]);
    }

    public function chats(Request $request, $teacherId)
    {
        $userId = session('LoggedUserInfo');

        if (!$request->session()->has('LoggedUserInfo')) {
            return redirect('user/login')->with('fail', 'You must be logged in to access the dashboard');
        }

        $LoggedUserInfo = User::find($userId);

        if (!$LoggedUserInfo) {
            return redirect('user/login')->with('fail', 'Invalid user ID');
        }

        $teacher = Teacher::find($teacherId);

        if (!$teacher) {
            return redirect('user/login')->with('fail', 'Invalid teacher ID');
        }

        return view('user.user_chat', [
            'LoggedUserInfo' => $LoggedUserInfo,
            'teacher' => $teacher,
        ]);
    }

    public function logout()
    {
        if (session()->has('LoggedUserInfo')) {
            session()->forget('LoggedUserInfo');
        }
        session()->flush();

        return redirect()->route('user.login');
    }
}
