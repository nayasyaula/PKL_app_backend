@extends('adminlte::page')

@section('content')
    <div class="jumbotron py-2 px-1 bg-light text-center mb-4">
        <br>
        <h1 style="color: #343a40;">
            To Do List
        </h1>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-end">
            <a href="{{ route('ToDoList.create') }}" class="btn btn-primary">Tambah</a>
        </div>  
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (count($todos) > 0)
                @foreach ($todos as $date => $tasks)
                    @php
                        $formattedDate = date('d M Y', strtotime($date));
                    @endphp
                    <div class="mb-4">
                        <h4>{{ $formattedDate }}</h4>
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Content</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $todo)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $todo->content }}</td>
                                        <td>
                                            <form action="{{ route('ToDoList.updateStatus', $todo->id) }}" method="POST" id="updateStatusForm{{ $todo->id }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="status" onchange="this.form.submit()" {{ $todo->status == 'Completed' ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ $todo->status }}</label>
                                                </div>
                                            </form>
                                        </td>                                
                                        <td>
                                            <a href="{{ route('ToDoList.edit', $todo->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                            <form action="{{ route('ToDoList.destroy', $todo->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah kamu yakin untuk menghapus ini?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">Tidak ada data To Do List yang tersedia.</div>
            @endif
        </div>
    </div>
@endsection
