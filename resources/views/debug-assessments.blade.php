<table class="table">
    <thead>
        <tr>
            <th>Database ID</th>
            <th>Order Field</th>
            <th>Creation Order</th>
            <th>Question</th>
        </tr>
    </thead>
    <tbody>
        @foreach($assessments as $assessment)
        <tr>
            <td>{{ $assessment->id }}</td>
            <td>{{ $assessment->order ?? 'N/A' }}</td>
            <td>{{ $assessment->created_at }}</td>
            <td>{{ $assessment->question }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
