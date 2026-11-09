<table>
    <thead>
        <tr>
            <th scope="col">Task ID</th>
            <th scope="col">Title</th>
            <th scope="col">Status</th>
            <th scope="col">Task Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($tasks as $task)
            <tr>
                <td>{{ $task->id }}</td>
                <td>{{ $task->title }}</td>
                <td>{{ ucfirst($task->status) }}</td>
                <td>{{ $task->task_date->format('Y-m-d') }}</td>
            </tr>
        @empty
            <tr><td colspan="4">{{ $emptyMessage }}</td></tr>
        @endforelse
    </tbody>
</table>
