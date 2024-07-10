@extends('adminlte::page')

@section('content')
    <div class="jumbotron py-2 px-1 bg-light text-center mb-4">
        <br>
        <h1 style="color: #343a40;">
            Users
        </h1>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <a href="{{ route('show.user', $user->id ) }}" class="btn btn-info btn-sm">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

<style>
    .jumbotron.py-2.px-1 {
        background-color: #f8f9fa;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 20px;
    }
    .jumbotron.py-2.px-1 h4 {
        font-size: 1.75rem;
        color: #343a40;
    }
    .table-responsive {
        margin-top: 20px;
    }
    .table {
        background-color: #ffffff;
    }
    .table thead.thead-dark th {
        background-color: #343a40;
        color: #fff;
    }
    .table tbody tr:hover {
        background-color: #f1f1f1;
    }
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
    }
</style>
