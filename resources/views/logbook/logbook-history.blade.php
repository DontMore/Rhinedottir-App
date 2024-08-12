<!-- logbook-history.blade.php -->

@extends('layout.main')

@section('container')
<h2>Logbook History</h2>
<hr>

<div class="row">
    <div class="col">Catalog Number</div>
    <div class="col">: {{ $reagen->noCatalog }}</div>
</div>
<div class="row">
    <div class="col">Reagent Name</div>
    <div class="col">: {{ $reagen->nameReagen }}</div>
</div>
<div class="row">
    <div class="col">Merk</div>
    <div class="col">: {{ $reagen->merk }}</div>
</div>
<div class="row">
    <div class="col">Batch</div>
    <div class="col">: 
        @foreach ($reagen->reagenIn as $stock)
            {{ $stock->batch }} 
            Expired Date : {{ $stock->expiredDate }}<br>
        @endforeach
    </div>
</div>
<div class="row">
    <div class="col">Stock</div>
    <div class="col">: {{ optional($reagen->stockReagen)->quantity ?? 'N/A' }}</div>
</div>

<h2>Logbook Information</h2>
<table class="table">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Quantity Taken</th>
            <th>User</th>
            <th>Note</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($logbookReagens as $logbook)
            <tr>
                <td>{{ $logbook->formatted_created_at }}</td>
                <td>{{ $logbook->quantity_taken }}</td>
                <td>{{ $logbook->user->name }}</td>
                <td>{{ $logbook->note }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection
