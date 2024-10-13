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
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">Attendance IN</th>
                            <th scope="col" class="text-center">Attendance OUT</th>
                            <th scope="col" class="text-center">In Status</th>
                            <th scope="col" class="text-center">Out Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($attendance as $item)
                            <tr>
                                <th scope="row" class="text-center">{{ $no++ }}</th>
                                <td class="text-center">{{ $item->in }}</td>
                                <td class="text-center">{{ $item->out }}</td>
                                <td class="text-center">{{ $item->in_status }}</td>
                                <td class="text-center">{{ $item->out_status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>                
            </div>
            <hr class="my-4">
            <h1>To-Do List</h1>
@if (session('success'))
    <div class="alert alert-info text-center">
        {{ session('success') }}
    </div>
@endif
<div class="card-body">
    @if (is_countable($todos) && count($todos) > 0)
        @foreach ($todos as $date => $tasks)
            @php
                $formattedDate = date('d M Y', strtotime($date));
            @endphp
            <div class="mb-4">
                <h4>{{ $formattedDate }}</h4>
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Content</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                            <th class="text-center">File</th> <!-- Kolom untuk File -->
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
                                    @if ($todo->status == 'Completed')
                                        <span class="badge badge-success">{{ $todo->status }}</span>
                                    @elseif ($todo->status == 'Pending')
                                        <span class="badge badge-warning">{{ $todo->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('user.updateStatus', $todo->id) }}" method="POST" id="updateStatusForm{{ $todo->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="status" onchange="this.form.submit()" {{ $todo->status == 'Completed' ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ $todo->status }}</label>
                                        </div>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <!-- Menampilkan ikon download untuk file -->
                                    @if ($todo->file_path)
                                        @php
                                            $fileExtension = pathinfo($todo->file_path, PATHINFO_EXTENSION);
                                        @endphp

                                        @if (in_array($fileExtension, ['jpg', 'jpeg', 'png']))
                                            <a href="{{ Storage::url($todo->file_path) }}" target="_blank">
                                                <i class="fas fa-file-image fa-lg"></i>
                                            </a>
                                        @elseif ($fileExtension == 'pdf')
                                            <a href="{{ Storage::url($todo->file_path) }}" target="_blank">
                                                <i class="fas fa-file-pdf fa-lg"></i>
                                            </a>
                                        @elseif ($fileExtension == 'docx')
                                            <a href="{{ Storage::url($todo->file_path) }}" target="_blank">
                                                <i class="fas fa-file-word fa-lg"></i>
                                            </a>
                                        @elseif (in_array($fileExtension, ['mp4', 'avi', 'mov']))
                                            <a href="{{ Storage::url($todo->file_path) }}" target="_blank">
                                                <i class="fas fa-file-video fa-lg"></i>
                                            </a>
                                        @else
                                            <p>Tidak ada file</p>
                                        @endif
                                    @else
                                        <p>Tidak ada file</p>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('user.pesan', $todo->id) }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="pesan" class="form-control" placeholder="Tambahkan pesan...">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-info btn-sm" title="Kirim Pesan">
                                                    <i class="fas fa-envelope"></i> <!-- Ganti dengan ikon yang diinginkan -->
                                                </button>                                            
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">Tidak ada data To-Do List yang tersedia.</div>
    @endif
</div>

            
        </div>
    </div>
@endsection
