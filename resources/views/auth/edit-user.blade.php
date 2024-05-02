@extends('layout.main')

@section('container')
    <h1>Edit User</h1>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('user.update', ['id' => $user->id]) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password :</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Re-Type Password:</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>

        @can('admin')
        <div class="form-group mb-3">
            <label for="role">Role</label>
            <select class="form-control" id="role" name="role" required>
                <option value="">Select a role</option>
                <option value="Admin" {{ old('role', $user->role) == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Analis" {{ old('role', $user->role) == 'Analis' ? 'selected' : '' }}>Analis</option>
            </select>
        </div>
        @endcan

        <!-- Add more fields if needed -->

        <button type="submit" class="btn btn-primary">Update User</button>
    </form>

@endsection
 
