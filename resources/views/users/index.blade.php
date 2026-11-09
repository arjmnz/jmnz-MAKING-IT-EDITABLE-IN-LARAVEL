@extends('layouts.app')

@section('title', 'User Accounts')

@section('content')
    <h1>User Accounts</h1>
    <p><a href="{{ route('users.create') }}">Create User</a></p>
    <table>
        <thead><tr><th>Avatar</th><th>Username</th><th>Full Name</th><th>Email</th><th>Action</th></tr></thead>
        <tbody>
        @forelse ($users as $user)
            <tr>
                <td><img src="{{ $user->avatar ? Storage::disk('public')->url($user->avatar) : asset('images/avatar-placeholder.png') }}" alt="Avatar for {{ $user->full_name }}" width="100" height="100"></td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->full_name }}</td>
                <td>{{ $user->email }}</td>
                <td><a href="{{ route('users.edit', $user) }}">Edit</a></td>
            </tr>
        @empty
            <tr><td colspan="5">No users found.</td></tr>
        @endforelse
        </tbody>
    </table>
@endsection
