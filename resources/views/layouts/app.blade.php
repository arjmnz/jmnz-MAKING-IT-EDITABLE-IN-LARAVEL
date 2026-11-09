<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tasks for Today')</title>
</head>
<body>
    <nav aria-label="Main navigation">
        <a href="{{ route('home') }}">Welcome</a> |
        <a href="{{ route('tasks.index') }}">Task List</a> |
        <a href="{{ route('profile') }}">Profile</a> |
        <a href="{{ route('about') }}">About</a> |
        <a href="{{ route('customers.index') }}">Customers</a> |
        <a href="{{ route('users.index') }}">User Accounts</a>
    </nav>
    <main>
        @if (session('success'))
            <p role="status">{{ session('success') }}</p>
        @endif
        @yield('content')
    </main>
</body>
</html>
