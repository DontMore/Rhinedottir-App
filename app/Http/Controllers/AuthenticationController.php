<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Menampilkan halaman registrasi pengguna.
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Menampilkan halaman registrasi tamu.
     */
    public function registerGuest()
    {
        return view('auth.register-guest');
    }

    /**
     * Menyimpan data pengguna baru ke dalam database.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name'     => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email'    => 'required|email|max:255|unique:users',
                'password' => 'required|min:6',
                'repassword' => 'required|same:password',
                'role'     => 'required|in:Admin,Analis,superadmin'
            ]);
            unset($validatedData['repassword']);

            // ✅ Otomatis isi organization_guid sesuai dengan pembuat akun
            if (auth()->check()) {
                $validatedData['organization_guid'] = auth()->user()->organization_guid;
            }

            // is_active akan otomatis true karena default di migration & model boot()
            User::create($validatedData);

            Alert::success('Success', 'User has been created successfully!');
            return redirect()->back()->with('success', 'User created successfully.');
        } catch (ValidationException $e) {
            $errorMessage = $e->validator->errors()->first();
            Alert::error('Error', $errorMessage)->showConfirmButton('OK', '#3085d6');
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
    }

    /**
     * Mengotentikasi pengguna berdasarkan kredensial yang diberikan.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            // 🔒 Cek status aktif/non-aktif
            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('loginError', 'Akun Anda tidak aktif. Hubungi administrator.');
            }

            $request->session()->regenerate();
            $role = strtolower(Auth::user()->role);

            switch ($role) {
                case 'admin':
                    return redirect()->route('dashboard.index');
                case 'analis':
                    return redirect()->route('logbook.index');
                case 'superadmin':
                    return redirect()->route('superadmin.index');
                default:
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()->with('loginError', 'Role tidak dikenali. Hubungi administrator.');
            }
        }

        return back()->with('loginError', 'Login failed!');
    }

    /**
     * Melakukan logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Menampilkan daftar semua pengguna dengan search & filter.
     */
    public function userList(Request $request)
    {
        $query = User::query();

        // 🔍 Search by username, name, or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // 🔽 Filter by Role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // 🔽 Filter by Status (is_active)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // 📊 Paginate & preserve query params
        $users = $query->orderBy('is_active', 'desc')
                       ->orderBy('name', 'asc')
                       ->paginate(10)
                       ->withQueryString();

        return view('auth.user-list', compact('users'));
    }

    /**
     * Menampilkan halaman edit untuk pengguna tertentu.
     */
    public function editUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('user.index')->with('error', 'User not found.');
        }
        return view('auth.edit-user', compact('user'));
    }

    /**
     * Menyimpan perubahan data pengguna.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id . ',guid',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id . ',guid',
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|in:Admin,Analis,superadmin',
            'is_active' => 'nullable|boolean',
        ]);

        $user = User::find($id);
        if (!$user) {
            return redirect()->route('user.index')->with('error', 'User not found.');
        }

        $user->name  = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->role  = $request->input('role');

        // Update status aktif/non-aktif jika dikirim
        if ($request->has('is_active')) {
            $user->is_active = $request->boolean('is_active');
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        alert()->success('Success', 'User has been updated successfully!');
        return redirect()->route('user.edit', ['id' => $id])->with('success', 'User updated successfully');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found or deletion failed.');
        }

        $user->delete();

        Alert::success('Deleted', 'User has been deleted successfully!');
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle status aktif/non-aktif pengguna.
     */
    public function toggleStatus($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        Alert::success('Success', "User has been {$status} successfully!");
        return redirect()->back()->with('success', "User {$status} successfully.");
    }
}