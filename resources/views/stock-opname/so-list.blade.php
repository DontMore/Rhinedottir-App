@extends('layout.main')

@section('container')

<div class="container mt-5">
    <h2 class="text-center mb-4">Data List</h2>

    <!-- Table -->
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Month</th>
                <th>Year</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->month }}</td>
                    <td>{{ $item->year }}</td>
                    <td>{{ $item->status == 1 ? 'Complete' : 'Not Complete' }}</td>
                    <td>
                        <a href="{{ route('soDetail', ['month' => $item->month, 'year' => $item->year]) }}" class="btn btn-primary btn-sm">
                            View
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
