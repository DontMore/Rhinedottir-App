@extends('layout.main')

@section('container')

<!-- baris 1 -->
<div class="row">
    <h2 class="mb-4">Register</h2>
</div><!-- baris 1 -->

<!-- baris 2 -->
<div class="row container">
    <!-- baris 2 kolom 1 -->
    <div class="col">
        <form method="POST" action="/register">
            @csrf
            <div class="form-group row">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group row">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
            </div>

            <div class="form-group row">
                <label for="username">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group row">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter a password" required>
            </div>
            <div class="form-group row">
                <label for="repassword">Re-enter Password</label>
                <input type="password" class="form-control" id="repassword" name="repassword" placeholder="Re-enter your password" required>
            </div>

            <div class="form-group row">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">Select a role</option>
                    <option value="Admin">Admin</option>
                    <option value="Analis">Analis</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary row">Submit</button>
        </form>
    </div><!-- baris 2 kolom 1 --> 
</div><!-- baris 2 -->

@endsection
 
