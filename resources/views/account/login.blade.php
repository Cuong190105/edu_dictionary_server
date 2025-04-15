@extends('layouts.auth')
@section('title', 'Login')
@section('substyle')
    @vite('resources/css/login.css')
@endsection
@section('card')
    <div class="sign-in-form auth-card">
        <h1>Sign in</h1>
        <form name='signIn' action="" method="post">
            @csrf
            <div class="text-input" id="usr">
                <input type="text" name="username" aria-label="Username" required/>
                <label>Username</label>
            </div>
            <div class="text-input" id="pwd">
                <input type="password" name="password" aria-label="Password" required/>
                <label>Password</label>
            </div>
            <div class="forget-credentials">
                <div class="transparent-btn">
                    <a href="forget" class="forget">
                        Forget account info?
                    </a>
                </div>
            </div>
            <button class="submit" type="submit">Sign In</button>
        </form>
        <div class="sign-up transparent-btn">
            <a href="{{ url('account/register') }}" class="forget">
                Create an account
            </a>
        </div>
    </div>
@endsection