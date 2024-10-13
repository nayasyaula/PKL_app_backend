@extends('adminlte::page')

@section('content')
    <div class="jumbotron py-2 px-1 bg-light text-center mb-4">
        <br>
        <h1 style="color: #343a40;">
            Edit To-Do List
        </h1>
    </div>
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('ToDoList.update', $todolist->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="content">Content</label>
                    <input type="text" name="content" class="form-control" id="content" placeholder="Content" value="{{ $todolist->content }}" required>
                </div>
                <div class="form-group">
                    <label for="keterangan">Description</label>
                    <input type="text" name="keterangan" class="form-control" id="keterangan" placeholder="keterangan" value="{{ $todolist->keterangan }}" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control" id="status" required>
                        <option value="Pending" {{ $todolist->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Completed" {{ $todolist->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" name="date" class="form-control" id="date" value="{{ $todolist->date }}" required>
                </div>
                <div class="form-group">
                    <label for="file">Upload File</label>
                    <input type="file" name="file" class="form-control" id="file" accept=".docx,.jpg,.jpeg,.png,.pdf,.mp4,.avi,.mov" />
                    @if ($todolist->file_path)
                        <div class="mt-2">
                            @php
                                $fileExtension = pathinfo($todolist->file_path, PATHINFO_EXTENSION);
                            @endphp
                
                            @if ($fileExtension == 'jpg' || $fileExtension == 'jpeg' || $fileExtension == 'png')
                                <img src="{{ Storage::url($todolist->file_path) }}" alt="Uploaded Photo" style="max-width: 100%; height: auto;">
                            @elseif ($fileExtension == 'pdf')
                                <iframe src="{{ Storage::url($todolist->file_path) }}" style="width: 100%; height: 400px;" frameborder="0"></iframe>
                            @elseif ($fileExtension == 'docx')
                                <iframe src="https://docs.google.com/viewer?url={{ Storage::url($todolist->file_path) }}&embedded=true" style="width: 100%; height: 400px;" frameborder="0"></iframe>
                            @elseif ($fileExtension == 'mp4' || $fileExtension == 'avi' || $fileExtension == 'mov')
                                <video controls style="max-width: 100%; height: auto;">
                                    <source src="{{ Storage::url($todolist->file_path) }}" type="video/{{ $fileExtension }}">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <p>File tidak dapat ditampilkan</p>
                            @endif
                        </div>
                    @endif
                </div>
                
                <a href="{{ route('ToDoList.index') }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@stop
