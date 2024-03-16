<!-- logbook-take.blade.php -->
@extends('layout.main')

@section('container')
<!-- baris 1 -->
<div class="row">
    <div class="col-md-11">
        <h1>User List</h1>
    </div>
    <div class="col-md-1">
        <a href="/register"><button class="btn btn-primary">Create</button></a>
    </div>
</div><!-- baris 1 -->

<!-- baris 2 -->
<div class="row">
    <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Aksi</th>
                    <!-- Tambahkan kolom-kolom lain sesuai kebutuhan -->
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            <div class="d-flex">
                                <div class="mr-2">
                                    <a href="{{ route('user.edit', ['id' => $user->id]) }}">
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                    </a>
                                </div>
                                <div>
                                    <form action="{{ route('user.delete', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger delete-btn btn-sm" onclick="confirmDelete()">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                        <!-- Tambahkan kolom-kolom lain sesuai kebutuhan -->
                    </tr>
                @endforeach
            </tbody>
        </table>
</div><!-- baris 2 -->

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
