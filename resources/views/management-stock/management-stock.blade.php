@extends('layout.main')

@section('container')

      

      <!-- Judul halaman  -->
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Management Stock</h1>
      </div>

      <!-- Tombol add reagen dan search -->
      <div class="row">
        <!-- tombol add reagan -->
        <div class="col-md-2">
          <a href="add-reagen"><button type="button" class="btn btn-success">Tambahkan</button></a>
        </div>

        <!-- search -->
        <div class="col-md-10">
          <!-- Form input Search -->
          <form action="{{ route('management-stock.index') }}" method="GET">
                <div class="input-group mb-3">
                  <input type="text" class="form-control" name="keyword" aria-describedby="button-addon2" placeholder="Nama Reagen | Merk">
                  <button class="btn btn-outline-primary" type="submit" id="button-addon2">Search</button>
                </div>
            </form>
        </div>
      </div>

      <div>
        <hr>
        <a href="reagen-in"><button class="btn btn-success">Reagen In</button></a>
        <a href="reagen-out"><button class="btn btn-warning">Reagen Out</button></a>
        <hr>
      </div>
          
      <table class="table table-hover table-sm">
        <thead>
          <tr>
            <th class="col-2">Catalog Number</th>
            <th class="col-5">Reagent Name</th>
            <th class="col-1">Merk</th>
            <th class="col-1">Quanlity</th>
            <th class="col-2">Action</th>
          </tr>
        </thead>
        <tbody>
      <!-- perulangan -->
          @foreach($reagens as $item)
          <tr>
            <td>{{ $item->noCatalog }}</td>
            <td>{{ $item->nameReagen }}</td>
            <td>{{ $item->merk }}</td>
            <td>{{-- Check if stockOpname relationship exists --}}
              @if($item->stockReagen)
                {{ $item->stockReagen->quantity }}
              @else
                0
            @endif
            </td>
            <td><!-- Button view -->
              <div class="row">
                  <div class="col">
                    <a href="{{ route('data.view', ['noCatalog' => $item->noCatalog]) }}">
                      <button type="button" class="btn btn-info btn-sm mr-2">view</button>
                    </a>
                  </div>

                  <!-- Button Add -->
                  <div class="col">
                    <a href="{{ route('reagen.addstock', ['noCatalog' => $item->noCatalog]) }}">
                      <button type="button" class="btn btn-success btn-sm mr-2">add</button>
                    </a>
                  </div>

                  <!-- Button Edit -->
                  <div class="col">
                    <a href="{{ route('data.edit', ['noCatalog' => $item->noCatalog]) }}">
                      <button type="button" class="btn btn-warning btn-sm mr-2">edit</button>
                    </a>
                  </div>

                  <!-- Button Delete -->
                  <div class="col">
                    <form action="{{ route('data.delete', ['noCatalog' => $item->noCatalog]) }}" method="POST">
                          @csrf
                          <button class="btn btn-danger confirm-button btn-sm" data-toggle="tooltip" title='Delete'>delete</button>
                    </form>
                  </div>
              </div>
            </td>
            </tr>
          @endforeach
            </tbody>
      </table>

      <!-- Tampilkan pagination links -->
      <div class="d-flex justify-content-center">
          {{ $reagens->links() }}
      </div>

      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
      <script type="text/javascript">

          $('.confirm-button').click(function(event) {
              var form =  $(this).closest("form");
              event.preventDefault();
              swal({
                  title: `Are you sure you want to delete this?`,
                  text: "It will gone forevert",
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
              })
                  .then((willDelete) => {
                      if (willDelete) {
                          form.submit();
                      }
                  });
          });
      </script>

@endsection
