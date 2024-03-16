@extends('layout.main')

@section('container')

<!-- baris 1 -->
<div class="row">
    <!-- baris 1 kolom 1 -->
    <div class="col-md-7">
        <h2>All Reagen Data</h2>
    </div> <!-- End baris 1 kolom 1 -->

    <!-- baris 1 kolom 2 -->
    <div class="col-md-5">
        <form action="{{ route('report.index') }}" method="get" class="form-inline mb-3">
            @csrf
            <label for="month" class="mr-2">Month:</label>
            <select name="month" id="month" class="form-control mr-2">
                <!-- Option untuk bulan, sesuaikan dengan kebutuhan Anda -->
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>

            <label for="year" class="mr-2">Year:</label>
            <input type="text" name="year" id="year" class="form-control mr-2" placeholder="Enter year">

            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div> <!-- End baris 1 kolom 2 -->
</div> <!-- End baris 1 -->
<hr>

<!-- Baris 2 -->
<div class="row">
    <!-- Baris 2 kolom 1 -->
    <div class="col-md-10">
        <h2>Report Reagen {{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</h2>
    </div>

    <!-- Baris 2 kolom 2 -->
    <div class="col-md-2">
        <!-- Tombol untuk generate laporan PDF -->
        <form action="{{ route('report.print') }}" method="get" class="form-inline mb-3">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn btn-success">Download Report</button>
        </form>
    </div>
</div> <!-- End Baris 2 -->

<!-- baris 3 -->
<div class="row">
    <table class="table table-bordered text-center table-hover">
        <thead>
            <tr>
                <th>No. Catalog</th>
                <th>Name Reagen</th>
                <th>Merk</th>
                <th>Pack Size</th>
                <th>Quantity</th>
                <th>Total Quantity In</th>
                <th>Total Quantity Taken</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reagens as $reagen)
                <tr>
                    <td>{{ $reagen->noCatalog }}</td>
                    <td>{{ $reagen->reagen->nameReagen }}</td>
                    <td>{{ $reagen->reagen->merk }}</td>
                    <td>{{ $reagen->reagen->packSize }}</td>
                    <td>{{ $reagen->quantity }}</td>
                    <td>{{ $reagen->quantity_in }}</td>
                    <td>{{ $reagen->quantity_out }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</div> <!-- End Baris 3 -->

@endsection
