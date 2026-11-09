@extends('layouts.app')

@section('title', 'Welcome - Tasks for Today')

@section('content')
    <h1>Welcome to Tasks for Today</h1>
    <p>Today's date: {{ $today->format('F j, Y') }} ({{ config('app.timezone') }})</p>
    <h2>Today's Tasks</h2>
    @include('tasks.table', ['emptyMessage' => 'No tasks scheduled for today.'])
@endsection
