@extends('layouts.auth')
@section('title', 'login')
@section('substyle')
    @vite('resources/css/login.css')
@endsection
@section('card')
    <div class="auth-card">
        <h1>Create an account</h1>
        <form name='register' method="POST">
            @csrf
            <div class="text-input">
                <input type="text" name="name" aria-label="Username" required/>
                <label>Display Name</label>
            </div>
            <div class="text-input">
                <input type="text" name="username" aria-label="Username" required/>
                <label>Username</label>
            </div>
            <div class="text-input">
                <input type="password" name="password" aria-label="Password" required/>
                <label>Password</label>
            </div>
            <div class="text-input">
                <input type="password" name="password_confirmation" aria-label="Retype password" required/>
                <label>Retype Password</label>
            </div>
            <div class="text-input">
                <input type="email" name="email" aria-label="Email" required/>
                <label>Email</label>
            </div>
            <button class="submit" type="submit">Register</button>
        </form>
        <div class="transparent-btn">
            <a href="{{ url('account/login') }}">
                <span>Already registered?</span>
            </a>
        </div>
    </div>
@endsection