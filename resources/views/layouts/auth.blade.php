@extends('layouts.app')
@section('style')
    @vite('resources/css/auth.css')
    @vite('resources/css/auth-card.css')
    @yield('substyle')
@endsection
@section('content')
    <div class="container">
        @yield('card')
    </div>
@endsection