@extends('layouts.app')

@section('title', 'Customer Form')

@section('content')
    <h1>{{ $customer->exists ? 'Edit Customer' : 'Create Customer' }}</h1>
    @if ($errors->any())
        <p role="alert">Please correct the errors below.</p>
    @endif
    <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
        @csrf
        @if ($customer->exists)
            @method('PUT')
        @endif
        <p>
            <label for="full_name">Full Name (required)</label><br>
            <input id="full_name" name="full_name" type="text" maxlength="255" value="{{ old('full_name', $customer->full_name) }}" required>
            @error('full_name') <br><span role="alert">{{ $message }}</span> @enderror
        </p>
        <p>
            <label for="email">Email (required)</label><br>
            <input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $customer->email) }}" required>
            @error('email') <br><span role="alert">{{ $message }}</span> @enderror
        </p>
        <p>
            <label for="phone">Phone (optional)</label><br>
            <input id="phone" name="phone" type="text" maxlength="20" value="{{ old('phone', $customer->phone) }}">
            @error('phone') <br><span role="alert">{{ $message }}</span> @enderror
        </p>
        <button type="submit">{{ $customer->exists ? 'Save Changes' : 'Create Customer' }}</button>
        <a href="{{ route('customers.index') }}">Cancel</a>
    </form>
@endsection
