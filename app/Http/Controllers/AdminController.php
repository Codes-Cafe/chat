<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //
    public function register()
    {
        return view("admin.register");
    }
    public function login()
    {
        return view("admin.login");
    }

    public function loginFunction(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5|max:12'
        ]);

        // Find the admin by email
        $adminInfo = Admin::where('email', $request->email)->first();

        // Check if the admin exists
        if (!$adminInfo) {
            return back()->withInput()->withErrors(['email' => 'Email not found']);
        }

        // Check if the admin's account is inactive
        if ($adminInfo->status === 'inactive') {
            return back()->withInput()->withErrors(['status' => 'Your account is inactive']);
        }

        // Check if the password is correct
        if (!Hash::check($request->password, $adminInfo->password)) {
            return back()->withInput()->withErrors(['password' => 'Incorrect password']);
        }

        // Set session variables
        session([
            'LoggedAdminInfo' => $adminInfo->id,
            'LoggedAdminName' => $adminInfo->name,
        ]);

        // Redirect to the admin dashboard
        return redirect()->route('admin.admin_dashboard');
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


        Admin::create($adminData);

        return redirect()->route('admin.login')->with('success', 'Admin created successfully!');
    }


    public function dashboard()
    {
        $adminId = session('LoggedAdminInfo');

        // Check if the session has the correct admin ID
        if (!$adminId) {
            return redirect('admin/login')->with('fail', 'You must be logged in to access the dashboard');
        }

        $LoggedAdminInfo = Admin::find($adminId);

        if (!$LoggedAdminInfo) {
            return redirect('admin/login')->with('fail', 'Admin not found');
        }

        return view('admin.admin_dashboard', [
            'LoggedAdminInfo' => $LoggedAdminInfo
        ]);
    }

    public function logout()
    {
        if (session()->has('LoggedAdminInfo')) {
            session()->forget('LoggedAdminInfo');
        }
        session()->flush();

        return redirect()->route('admin.login');
    }
}
