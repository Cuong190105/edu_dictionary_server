@extends('layouts.auth')
@section('title', 'Forget credentials')
@section('substyle')
    @vite('resources/css/forget.css')
@endsection
@section('card')
    <div class="auth-card" id="forget">
        <h1>Restore credentials</h1>
        <form>
            <div class="form-section" id="select-creds">
                <h2>What information do you forget?</h2>
                <div class="form-selection">
                    <input name="forget" class="forget-select" id="usrn" type="radio">
                    <label for="usrn">
                        <div class="option">
                            Username
                        </div>
                    </label>
                    <input name="forget" class="forget-select" id="pwd" type="radio">
                    <label for="pwd">
                        <div class="option">
                            Password
                        </div>
                    </label>
                </div>
            </div>
            <div class="forget-usrn-form">
                <div class="enter-email">
                    <label for="email">Enter your registering email:</label><br>
                    <div class="text-input">
                        <input type="email" name="email" id="email" required>
                    </div>
                </div>
            </div>
            <div class="forget-pwd-form">
                <div class="enter-usrn">
                    <label for="usrn">Enter your username:</label><br>
                    <div class="text-input">
                        <input type="text" name="username" id="usrn" required>
                    </div>
                </div>
            </div>
            <input type="submit" class="submit" value="Next">
        </form>
    </div>
@endsection