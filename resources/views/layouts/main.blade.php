@extends('layouts.app')
@section('style')
    @vite('resources/css/main.css')
    @yield('substyle')
@endsection
@section('content')
    <header>
        <div class="menu">
            <div class="transparent-btn round-btn">

            </div>
            <div class="page-name">

            </div>
        </div>
        <div class="search">
            <form action="" id="searchbar" class="search-bar" method="GET">
                <button type="submit" class="transparent-btn round-btn" id="search-btn">
                    <img src="{{ asset('img/search/search.png') }}" alt="Search" class="search-icon">
                </button>
                <input name="searchfield" id="searchfield" autocomplete="off" class='search-content' type="text" placeholder='Type here to search'>
                <button type="reset" class="transparent-btn round-btn" id="clear-btn" style="visibility: hidden">
                    <img src="{{ asset('img/search/delete.png') }}" alt="Search" class="search-icon">
                </button>
            </form>
            <div class="transparent-btn" id="filter-btn">

            </div>
        </div>
        <div class="settings">
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button type=submit class="logout round-btn">
                    <img src="{{ asset('img/settings/logout.png') }}" alt="Logout" class="logout-icon">
                </button>
            </form>
        </div>
    </header>
    <div class="container">
        @yield('subcontent')
    </div>
    @vite('resources/js/searchfunction.js')
@endsection