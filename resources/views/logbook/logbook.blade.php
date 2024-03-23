@extends('layout.main')

@section('container')

<!-- Baris 1 -->
    <div class="row border-bottom">
        <!-- Baris 1 kolom 1 -->
        <div class="col-md-4">
            <h1 class="h2">Logbook</h1>
        </div><!-- Baris 1 kolom 1 -->
        
        <!-- Baris 1 kolom 2 -->
        <div class="col-md-4 ml-auto">
            <div class="wrap">
                <div class="search mb-3">
                <form class="search form-inline" action="{{ route('logbook.index') }}" method="GET">
                    <div class="input-group mb-3">
                    <input type="text" class="form-control" name="keyword" aria-describedby="button-addon2" placeholder="Nama Reagen | Merk">
                    <button class="btn btn-outline-primary" type="submit" id="button-addon2">Search</button>
                    </div>
                </form>
                </div>
            </div>
        </div><!-- Baris 1 kolom 2 -->
    </div><!-- Baris 1 -->

    <div class="row">

    </div>

        <div class="table-responsive">
          <table class="table table-sm table-hover">
              <thead>
                  <tr>
                      <th scope="col">No</th>
                      <th scope="col">Nomor Katalog</th>
                      <th scope="col">Nama Reagen</th>
                      <th scope="col">Merk</th>
                      <th scope="col">Jumlah</th>
                      <th scope="col">Aksi</th>
                  </tr>
              </thead>
              <tbody>
                @foreach($reagens as $index => $reagen)
                    <tr>
                      <th scope="row">{{ $index + 1 }}</th>
                      <td><a href="{{ route('data.history', ['noCatalog' => $reagen->noCatalog]) }}">{{ $reagen->noCatalog }}</a></td>
                      <td><a href="{{ route('data.history', ['noCatalog' => $reagen->noCatalog]) }}">{{ $reagen->nameReagen }}</a></td>
                      <td>{{ $reagen->merk }}</td>
                      <td>
                        {{-- Check if stockOpname relationship exists --}}
                        @if($reagen->stockReagen)
                            {{ $reagen->stockReagen->quantity }}
                            @else
                            0
                        @endif
                      </td>
                      <td>
                          <a href="{{ route('data.history', ['noCatalog' => $reagen->noCatalog]) }}" class="btn btn-primary btn-sm">View</a>
                          <a href="{{ route('data.take', ['noCatalog' => $reagen->noCatalog]) }}" class="btn btn-primary btn-sm">Take</a>
                      </td>
                  </tr>
                @endforeach
              </tbody>
          </table>
        </div>

    <!-- Tampilkan pagination links -->
    <div class="d-flex justify-content-center">
        {{ $reagens->links() }}
    </div>

@endsection
