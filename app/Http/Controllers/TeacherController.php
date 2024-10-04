<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    //
    public function register()
    {
        return view("teacher.register");
    }
    public function login()
    {
        return view("teacher.login");
    }

    public function loginFunction(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5|max:12'
        ]);

        $adminInfo = Teacher::where('email', $request->email)->first();

        if (!$adminInfo) {
            return back()->withInput()->withErrors(['email' => 'Email not found']);
        }


        if ($adminInfo->status === 'inactive') {
            return back()->withInput()->withErrors(['status' => 'Your account is inactive']);
        }

        // Check if the password is correct
        if (!Hash::check($request->password, $adminInfo->password)) {
            return back()->withInput()->withErrors(['password' => 'Incorrect password']);
        }

        // Set session variables
        session([
            'LoggedTeacherInfo' => $adminInfo->id,
            'LoggedTeacherName' => $adminInfo->name,
        ]);

        // Redirect to the admin dashboard
        return redirect()->route('teacher.teacher_dashboard');
    }

    public function registerFucntion(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|regex:/^\S*$/',
        ], [
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 8 characters long.',
            'picture.max' => 'Profile picture size must be less than 2MB.',
        ]);

        $adminData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];


        Teacher::create($adminData);

        return redirect()->route('teacher.login')->with('success', 'Teacher created successfully!');
    }


    public function dashboard()
    {
        $adminId = session('LoggedTeacherInfo');

        // Check if the session has the correct admin ID
        if (!$adminId) {
            return redirect('teacher/login')->with('fail', 'You must be logged in to access the dashboard');
        }

        $LoggedAdminInfo = Teacher::find($adminId);

        if (!$LoggedAdminInfo) {
            return redirect('teacher/login')->with('fail', 'Teacher not found');
        }

        return view('teacher.teacher_dashboard', [
            'LoggedAdminInfo' => $LoggedAdminInfo
        ]);
    }

    public function chats(Request $request, $userid)
    {
        $teacherId = session('LoggedTeacherInfo');

        if (!$request->session()->has('LoggedTeacherInfo')) {
            return redirect('teacher/login')->with('fail', 'You must be logged in to access the dashboard');
        }

        $LoggedTeacherInfo = Teacher::find($teacherId);

        if (!$LoggedTeacherInfo) {
            return redirect('teacher/login')->with('fail', 'Invalid user ID');
        }

        $user = User::find($userid);

        if (!$user) {
            return redirect('teacher/login')->with('fail', 'Invalid teacher ID');
        }

        return view('teacher.teacher_chat', [
            'LoggedTeacherInfo' => $LoggedTeacherInfo,
            'user' => $user,
        ]);
    }

    public function logout()
    {
        if (session()->has('LoggedTeacherInfo')) {
            session()->forget('LoggedTeacherInfo');
        }
        session()->flush();

        return redirect()->route('teacher.login');
    }
}
