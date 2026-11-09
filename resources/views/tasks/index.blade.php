@extends('layouts.app')

@section('title', 'Task List - Tasks for Today')

@section('content')
    <h1>Task List</h1>
    <p>All tasks, ordered by date from earliest to latest.</p>
    @include('tasks.table', ['emptyMessage' => 'No tasks found.'])
@endsection
