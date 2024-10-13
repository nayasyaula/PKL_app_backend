@extends('adminlte::page')

@section('content')
    <div class="jumbotron py-2 px-1 bg-light text-center mb-4">
        <h1 style="color: #343a40;">Upload File for To-Do: {{ $todo->content }}</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('ToDoList.uploadFile.store', $todo->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file">Choose File</label>
                    <input type="file" name="file" class="form-control" accept=".docx,.jpg,.jpeg,.png,.pdf,.mp4,.avi,.mov" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
                <a href="{{ route('ToDoList.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
