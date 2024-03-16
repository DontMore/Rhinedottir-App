@extends('layout.main')

@section('container')
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">Order</h1>

            <a href="/new-order-form"><button class="btn btn-primary">New Order</button></a>
          </div>

          <table class="table">
            <thead>
                <tr>
                    <th class="col-1">ID</th>
                    <th class="col-1">No Catalog</th>
                    <th class="col-2">Name Reagen</th>
                    <th class="col-1">Merk</th>
                    <th class="col-1">Pack Size</th>
                    <th class="col-1">Quantity</th>
                    <th class="col-1">User ID</th>
                    <th class="col-1">Status</th>
                    <th class="col-1">Tanggal</th>
                    <th class="col-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->noCatalog }}</td>
                        <td>{{ $order->nameReagen }}</td>
                        <td>{{ $order->merk }}</td>
                        <td>{{ $order->packSize }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>
                            <?php
                              if( $order->status == 0){
                                echo '<span class="badge text-bg-warning">Not Complate</span>';
                              }else{
                                echo '<span class="badge text-bg-success">Complate</span>';
                              }
                            ?>
                        </td>
                        <td>{{ $order->created_at }}</td>
                        <td>
                            <div class="d-flex">
                                <div class="mr-2">
                                    <a href="{{ route('order.view', ['id' => $order->id]) }}" class="btn btn-info btn-sm">View</a>
                                </div>
                                <div class="">
                                    <form action="{{ route('order.delete', $order->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger delete-btn btn-sm" onclick="confirmDelete()">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <script>
          $(document).ready(function () {
            $('.delete-btn').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
</script>
@endsection
