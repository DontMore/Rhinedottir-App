@extends('layout.main')

@section('container')

  <div class="container-fluid">
    <!-- baris 1 -->
    <div class="row baris-1">
      <!-- baris 1 kolom 1 -->
      <div class="card col m-1 reagen-varian">
        <div class="card-body text-center">
          <h2>{{ $totalReagen }}</h2>
          <p>Reagent Varian</p>
        </div>
      </div><!-- baris 1 kolom 1 -->

      <!-- baris 1 kolom 2 -->
      <div class="card col m-1 total-reagen">
        <div class="card-body text-center">
          <h2><h2>{{ $stockReagen->quantity ?? 0 }}</h2></h2>
          <p>Total Reagent</p>
        </div>
      </div><!-- baris 1 kolom 2 -->

      <!-- baris 1 kolom 3 -->
      <div class="card col m-1 quantity-in">
        <div class="card-body text-center">
          <h2>{{ $stockReagen->quantity_in ?? 0 }}</h2>
          <p>Reagent In</p>
        </div>
      </div><!-- baris 1 kolom 3 -->

      <!-- baris 1 kolom 4 -->
      <div class="card col m-1 reagen-out">
        <div class="card-body text-center">
          <h2>{{ $stockReagen->quantity_out ?? 0 }}</h2>
          <p>Reagent Taken</p>
        </div>
      </div><!-- baris 1 kolom 4 -->
    </div> <!-- baris 1 -->

    <!-- Baris 3 -->
    <div class="row baris-3">

      <!-- baris 3 kolom 1 -->
      <div class="card col-md-3 m-1 p-0">
        <div class="card-body text-center">
          <p>Stock Kosong</p>
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th scope="col">Catalog Number</th>
                <th scope="col">Reagen Name</th>
                <th scope="col">Quantity</th>
              </tr>
            </thead>
            <tbody>
          @foreach($stockKosong as $item)
              <tr>
                <td>{{ $item->noCatalog }}</td>
                <td>{{ $item->reagen->nameReagen }}</td>
                <td>{{ $item->quantity }}</td>
              </tr>
          @endforeach
            </tbody>
          </table>
        </div>
      </div><!-- baris 3 kolom 4 -->

      <!-- baris 3 kolom 1 -->
      <div class="card col-md-3 m-1 p-0">
        <div class="card-body text-center">
          <p>Reagen Order</p>
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th scope="col">Catalog Number</th>
                <th scope="col">Reagen Name</th>
                <th scope="col">Quantity</th>
              </tr>
            </thead>
            <tbody>
          @foreach($reagenOrder as $order)
            <tr>
              <td>{{ $order->noCatalog }}</td>
              <td>{{ $order->nameReagen }}</td>
              <td>{{ $order->quantity }}</td>
            </tr>
          @endforeach
            </tbody>
          </table>
        </div>
      </div><!-- baris 3 kolom 4 -->

      <!-- baris 3 kolom 1 -->
      <div class="card col m-1 p-0">
        <div class="card-body text-center">
          <p>Regent Expired</p>
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th scope="col">Catalog Number</th>
                <th scope="col">Reagen Name</th>
                <th scope="col">Quantity</th>
                <th scope="col">ExpireD Date</th>
              </tr>
            </thead>
            <tbody>
          @foreach($reagenED as $expired)
              <tr>
              <td>{{ $expired->noCatalog }}</td>
              <td>{{ $expired->reagen ? $expired->reagen->nameReagen : 'Unknown' }}</td> <!-- Tambahkan pengecekan -->
              <td>{{ $expired->quantity }}</td>
              <td>{{ $expired->expiredDate}}</td>
              </tr>
          @endforeach
            </tbody>
          </table>
        </div>
      </div><!-- baris 3 kolom 4 -->
    </div><!-- Baris 3 -->

</div>

@endsection
