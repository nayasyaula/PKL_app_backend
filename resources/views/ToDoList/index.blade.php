@extends('adminlte::page')

@section('content')
    <div class="jumbotron py-2 px-1 bg-light text-center mb-4">
        <br>
        <h1 style="color: #343a40;">
            To-Do List
        </h1>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-end align-items-center">
            <a href="{{ route('ToDoList.create') }}" class="btn btn-secondary mt-3 me-3 btn-circle" title="Tambah" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                <i class="fas fa-plus"></i>
            </a>
            <a href="{{ route('word.tdl') }}" class="btn btn-primary mt-3" title="Export" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                <i class="fas fa-file-download"></i>
            </a>
        </div>         
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-info">{{ session('success') }}</div>
            @endif

            <!-- Tabel tetap ditampilkan meskipun datanya kosong -->
            <div class="mb-4">
                @if (count($todos) > 0)
                    @foreach ($todos as $date => $tasks)
                        @php
                            $formattedDate = date('d M Y', strtotime($date));
                        @endphp

                        <!-- Tanggal ditempatkan di luar tabel -->
                        <div class="text-center mb-3">
                            <strong>{{ $formattedDate }}</strong>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Content</th>
                                        <th class="text-center">Description</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">File</th> <!-- Kolom File -->
                                        <th class="text-center">Action</th>
                                        <th class="text-center">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tasks as $todo)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $todo->content }}</td>
                                            <td class="text-center">{{ $todo->keterangan }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('ToDoList.updateStatus', $todo->id) }}" method="POST" id="updateStatusForm{{ $todo->id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="status" onchange="this.form.submit()" {{ $todo->status == 'Completed' ? 'checked' : '' }}>
                                                        <label class="form-check-label">{{ $todo->status }}</label>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-center">
                                                @if ($todo->file_path)
                                                    @php
                                                        $fileExtension = pathinfo($todo->file_path, PATHINFO_EXTENSION);
                                                    @endphp
                                            
                                                    @if (in_array($fileExtension, ['jpg', 'jpeg', 'png']))
                                                        <a href="{{ asset('storage/' . $todo->file_path) }}" class="btn btn-success btn-sm" title="Download">
                                                            <i class="fas fa-file-image"></i> <!-- Ikon untuk gambar -->
                                                        </a>
                                                    @elseif ($fileExtension == 'pdf')
                                                        <a href="{{ asset('storage/' . $todo->file_path) }}" class="btn btn-success btn-sm" title="Download">
                                                            <i class="fas fa-file-pdf"></i> <!-- Ikon untuk PDF -->
                                                        </a>
                                                    @elseif ($fileExtension == 'docx')
                                                        <a href="{{ asset('storage/' . $todo->file_path) }}" class="btn btn-success btn-sm" title="Download">
                                                            <i class="fas fa-file-word"></i> <!-- Ikon untuk Word -->
                                                        </a>
                                                    @elseif (in_array($fileExtension, ['mp4', 'avi', 'mov']))
                                                        <a href="{{ asset('storage/' . $todo->file_path) }}" class="btn btn-success btn-sm" title="Download">
                                                            <i class="fas fa-file-video"></i> <!-- Ikon untuk video -->
                                                        </a>
                                                    @else
                                                        <p>Tidak ada file</p>
                                                    @endif
                                                @else
                                                    <!-- Jika belum, tampilkan ikon upload -->
                                                    <form action="{{ route('ToDoList.uploadFile', $todo->id) }}" method="POST" enctype="multipart/form-data" style="display: inline-block;">
                                                        @csrf
                                                        <input type="file" name="file" accept=".docx,.jpg,.jpeg,.png,.pdf,.mp4,.avi,.mov" required>
                                                        <button type="submit" class="btn btn-primary btn-sm" title="Upload">
                                                            <i class="fas fa-upload"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            
                                            
                                            <td class="text-center">
                                                <a href="{{ route('ToDoList.edit', $todo->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('ToDoList.destroy', $todo->id) }}" method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('Are you sure to delete this?')" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                                
                                                
                                            </td>
                                            <td>{{ $todo->pesan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @else
                    <!-- Jika data kosong, tetap tampilkan tabel -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Content</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">File</th> <!-- Kolom File -->
                                    <th class="text-center">Action</th>
                                    <th class="text-center">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">No To-Do List data available.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
