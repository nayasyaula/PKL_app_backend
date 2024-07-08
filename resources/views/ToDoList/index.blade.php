@extends('adminlte::page')

@section('content')
    <div class="d-block justify-content-between flex-wrap flex-end-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h5 class="h5">Data To Do List</h5>
        <div class="d-flex">
            <h6>Home / <b class="text-primary"> Data To Do List</b></h6>
        </div>  
    </div>
    <div class="card">
        <div class="d-flex justify-content-end">
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
                    <h4>{{ $formattedDate }}</h4>
                    <table class="table table-bordered">
                        <thead>
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
                                            <input type="checkbox" name="status" onchange="this.form.submit()" {{ $todo->status == 'Completed' ? 'checked' : '' }}>
                                            <span>{{ $todo->status }}</span>
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
                @endforeach
            @else
                <div class="alert alert-info">Tidak ada data To Do List yang tersedia.</div>
            @endif
        </div>
    </div>
@endsection