@extends('layout.main')

@section('container')

<!-- baris 3 -->
<div class="row">
    <table class="table table-bordered table-hover table-sm">
        <thead>
            <tr>
                <th class="text-nowrap" style="width: 15%;">Month</th>
                <th class="text-nowrap" style="width: 15%;">Year</th>
                <th class="text-nowrap" style="width: 20%;">Status</th>
                <th class="text-nowrap" style="width: 15%;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report as $data)
                <tr>
                    <td class="text-nowrap">{{ $data->month }}</td>
                    <td class="text-nowrap">{{ $data->year }}</td>
                    <td class="text-nowrap">{{ $data->status == 1 ? 'Complete' : 'Not Complete' }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('reportDetail', ['month' => $data->month, 'year' => $data->year]) }}" class="btn btn-primary btn-sm">
                            View
                        </a></button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> <!-- End Baris 3 -->

@endsection
