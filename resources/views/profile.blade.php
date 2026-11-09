@extends('layouts.app')

@section('title', 'Profile - Tasks for Today')

@section('content')
    <h1>Profile</h1>
    @if ($user)
        <dl>
            <dt>Username</dt><dd>{{ $user->username }}</dd>
            <dt>Full Name</dt><dd>{{ $user->full_name }}</dd>
            <dt>Email</dt><dd>{{ $user->email }}</dd>
        </dl>
    @else
        <p>No profile found.</p>
    @endif
@endsection
