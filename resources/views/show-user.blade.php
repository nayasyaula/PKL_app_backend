@extends('adminlte::page')

@section('content')
    <div class="jumbotron py-3 px-4 bg-light text-center">
        <h4 class="display-4 text-dark">
            <b>About</b> <i>{{ $user->name }}</i>
        </h4>
        <hr class="my-4">
        <div class="text-left">
            <h1 class="mb-4">Attendance</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Attendance IN</th>
                            <th>Attendance OUT</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($attendance as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item->in }}</td>
                                <td>{{ $item->out }}</td>
                                <td>{{ $item->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <hr class="my-4">
            <h1>To Do List</h1> 
            <!-- Add To Do List content here -->
        </div>
    </div>
@endsection

<style>
    .jumbotron {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 20px;
    }
    .display-4 {
        font-size: 2.5rem;
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
</style>
