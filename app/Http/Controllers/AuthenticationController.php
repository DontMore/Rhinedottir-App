<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\ValidationException;

/**
 * Controller untuk menangani autentikasi dan manajemen pengguna.
 * Kelas ini bertanggung jawab atas proses login, registrasi, logout,
 * serta operasi CRUD untuk pengguna dalam aplikasi.
 */
class AuthenticationController extends Controller
{
    /**
     * Menampilkan halaman login.
     *
     * @return \Illuminate\View\View
     */
    public function login(){
        return view('auth.login');
    }

    /**
     * Menampilkan halaman registrasi pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function register(){
        return view('auth.register');
    }

    /**
     * Menampilkan halaman registrasi tamu.
     *
     * @return \Illuminate\View\View
     */
    public function registerGuest(){
        return view('auth.register-guest');
    }

    /**
     * Menyimpan data pengguna baru ke dalam database.
     * Melakukan validasi input dan menangani kesalahan validasi.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'username' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'repassword' => 'required|same:password',
                'role' => 'required'
            ]);

            unset($validatedData['repassword']);

            User::create($validatedData);

            Alert::success('Success', 'User has been created successfully!');

            return redirect()->back()->with('success', 'User created successfully.');
        } catch (ValidationException $e) {
            $errorMessage = $e->validator->errors()->first();
            // Menggunakan Alert::error untuk menampilkan alert error
            Alert::error('Error', $errorMessage)->showConfirmButton('OK', '#3085d6');

            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
    }

    /**
     * Mengotentikasi pengguna berdasarkan kredensial yang diberikan.
     * Mengarahkan pengguna ke halaman yang sesuai berdasarkan peran mereka.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request){

        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Periksa peran pengguna setelah otentikasi
            if (Auth::user()->role === 'Admin') {
                return redirect()->intended('dashboard');
            } elseif (Auth::user()->role ===  'Analis') {
                return redirect()->intended('logbook');
            } elseif (Auth::user()->role === 'superadmin') {
                return redirect()->intended('superadmin');
            }
        }

        return back()->with('loginError', 'Login failed!');
    }

    /**
     * Melakukan logout pengguna dan mengakhiri sesi.
     * Mengarahkan pengguna kembali ke halaman login.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request){
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Menampilkan daftar semua pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function userList()
    {
        $users = User::all(); // Mengambil semua data pengguna dari tabel users

        return view('auth.user-list', compact('users'));
    }

    /**
     * Menampilkan halaman edit untuk pengguna tertentu.
     *
     * @param int $id ID pengguna yang akan diedit
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editUser($id){
        // Fetch the user by ID
        $user = User::find($id);

        // Check if the user is found
        if (!$user) {
            // You may handle the case where the user is not found, for example, redirect to the user list page
            return redirect()->route('user.index')->with('error', 'User not found.');
        }

        return view('auth.edit-user', compact('user'));
    }

    /**
     * Menyimpan perubahan data pengguna ke dalam database.
     * Melakukan validasi input dan menangani pembaruan password jika diperlukan.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID pengguna yang akan diperbarui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:Admin,Analis,superadmin',
            // Add more validation rules if needed
        ]);

        $user = User::find($id);
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->role = $request->input('role');

        // Check if the password is not empty before updating
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password')); // Gunakan bcrypt untuk mengenkripsi password
        }

        $user->save();

        // Pemberitahuan sukses menggunakan SweetAlert
        alert()->success('Success', 'User has been updated successfully!');

        return redirect()->route('user.edit', ['id' => $id])->with('success', 'User updated successfully');
    }

    /**
     * Menghapus pengguna dari database berdasarkan ID.
     *
     * @param int $userId ID pengguna yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($userId)
    {
    $deleted = User::destroy($userId);
    
    if (!$deleted) {
        // Handle user not found or deletion failed
        return redirect()->back()->with('error', 'User not found or deletion failed.');
    }

    Alert::success('Deleted', 'User has been deleted successfully!');

    return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
