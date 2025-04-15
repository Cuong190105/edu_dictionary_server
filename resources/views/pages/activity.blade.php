@extends('layouts.main')
@section('substyle')
    @vite('resources/css/activity.css')
@endsection
@section('subcontent')
    <div class="title"><h2>All Requests</h2></div>
    <div class="requests">
        <table class="request-history">
            <thead>
                <th>Timestamp</th>
                <th>UID</th>
                <th>Usage</th>
                <th>Status</th>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
@endsection