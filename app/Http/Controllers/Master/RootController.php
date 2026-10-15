<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class RootController extends Controller
{
    public function renderDashboard()
    {
        $data = [
            'menus' => 'Dashboard',
            'pages' => 'Master Dashboard',
            'spref' => '',
        ];

        return view('central.main-content', $data);
    }

    public function renderProfile()
    {
        $data = [
            'menus' => 'Profile',
            'pages' => 'Profile Settings',
            'spref' => '',
        ];

        return view('central.back-content', $data);
    }

    public function handleProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Update basic info
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('images/profile', $photoName, 'public');
            $user->photo = asset('storage/' . $photoPath);
        }

        // Handle password change
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                Alert::error('Error', 'Current password is incorrect.');
                return back();
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        Alert::success('Success', 'Profile updated successfully.');
        return back();
    }

    public function renderAbsensi()
    {
        $data = [
            'menus' => 'Absensi',
            'pages' => 'Attendance Management',
            'spref' => '',
        ];

        return view('central.back-content', $data);
    }

    public function handleLogout()
    {
        Auth::logout();
        Alert::success('Success', 'You have been logged out successfully.');
        return redirect()->route('signin');
    }
}
