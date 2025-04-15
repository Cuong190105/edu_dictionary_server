@extends('layouts.app')
@section('title', 'Home')
@section('style')
    <!-- <link rel="stylesheet" href="{{ asset('css/homepage.css') }}"> -->
    @vite('resources/css/homepage.css')
@endsection
@section('content')
    <div class="container">
        <div class="welcome-text">
            <h1 class="introduction">
                My resource manager
            </h1>
            <h2 class="get-started">
                Let's get started
            </h2>
            <div class="auth-action">
                <div class="sign-in btn">
                    <a class="sign-in-link" href="{{ url('/account/login') }}">
                        <span>Sign In</span>
                    </a>
                </div>
                <div class="sign-up btn">
                    <a class="sign-up-link" href="{{ url('/account/register') }}">
                        <span>Sign Up</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="welcome-img">
            <img src="{{ asset('img/welcome.jpg') }}" alt="Welcome!">
        </div>
    </div>
@endsection