@extends('layouts.app')

@section('title', 'User Form')

@section('content')
    <h1>{{ $user->exists ? 'Edit User' : 'Create User' }}</h1>
    @if ($errors->any())
        <p role="alert">Please correct the errors below.</p>
    @endif
    <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($user->exists)
            @method('PUT')
        @endif
        <p>
            <label for="username">Username (required)</label><br>
            <input id="username" name="username" type="text" maxlength="255" value="{{ old('username', $user->username) }}" required>
            @error('username') <br><span role="alert">{{ $message }}</span> @enderror
        </p>
        <p>
            <label for="full_name">Full Name (required)</label><br>
            <input id="full_name" name="full_name" type="text" maxlength="255" value="{{ old('full_name', $user->full_name) }}" required>
            @error('full_name') <br><span role="alert">{{ $message }}</span> @enderror
        </p>
        <p>
            <label for="email">Email (optional)</label><br>
            <input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $user->email) }}">
            @error('email') <br><span role="alert">{{ $message }}</span> @enderror
        </p>
        @if ($user->exists)
            <p><img src="{{ $user->avatar ? Storage::disk('public')->url($user->avatar) : asset('images/avatar-placeholder.png') }}" alt="Current avatar" width="100" height="100"></p>
            <p>
                <label for="avatar">Profile picture (optional, maximum 2 MB)</label><br>
                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
                @error('avatar') <br><span role="alert">{{ $message }}</span> @enderror
            </p>
            <p>JPG, PNG, GIF or WebP. Saved as a 200 by 200 pixel PNG thumbnail. Leave empty to keep the current image. After a validation error, select the file again.</p>
        @endif
        <button type="submit">{{ $user->exists ? 'Save Changes' : 'Create User' }}</button>
        <a href="{{ route('users.index') }}">Cancel</a>
    </form>
@endsection
