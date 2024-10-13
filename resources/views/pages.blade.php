@extends('adminlte::page')

@section('content')
    <div class="jumbotron py-2 px-1 bg-light text-center mb-4">
        <br>
        <h1 style="color: #343a40;">
            Attendance Today
        </h1>
    </div>

    @php
        $todayAttendance = $attendance->where('created_at', '>=', \Carbon\Carbon::today())->first();
    @endphp

    <div class="d-flex justify-content-center mb-4">
        <form action="{{ !$todayAttendance ? route('store') : route('update', $todayAttendance->id) }}" method="POST" class="card p-5 shadow border-0" style="width: 50%;">
            @csrf
            @if ($todayAttendance)
                @method('PATCH')
                <input type="hidden" name="out" id="outField">
                <button type="submit" class="btn btn-danger mt-3" id="attendanceButton">Attendance OUT</button>
            @else
                <input type="hidden" name="in" id="inField">
                <button type="submit" class="btn btn-primary mt-3" id="attendanceButton">Attendance IN</button>
            @endif
        </form>
    </div>

    <div class="card mb-4" style="width: 100%;">
        <div class="card-header d-flex justify-content-end align-items-center">
            <a href="{{ route('word.attendance') }}" class="btn btn-secondary mt-3 me-3 btn-circle" title="Export" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                <i class="fas fa-file-download"></i>
            </a>
            <a href="{{ route('generate-qr') }}" class="btn btn-primary mt-3 me-3" title="QR" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                <i class="fas fa-qrcode"></i>
            </a>
        </div>

        <div class="card-body">
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
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const attendanceButton = document.getElementById('attendanceButton');
            const inField = document.getElementById('inField');
            const outField = document.getElementById('outField');
            const todayAttendance = @json($todayAttendance);

            attendanceButton.addEventListener('click', function () {
                if (todayAttendance === null || todayAttendance.in === null) {
                    inField.value = new Date().toISOString();
                } else if (todayAttendance.out === null) {
                    outField.value = new Date().toISOString();
                } else {
                    attendanceButton.disabled = true;
                }
            });

            if (todayAttendance !== null && todayAttendance.out !== null) {
                attendanceButton.disabled = true;
            }
        });
    </script>
@endsection

<style>
    .jumbotron.py-2.px-1 {
        background-color: #f8f9fa;
        border-radius: 5px;
        text-align: center;
    }
    .jumbotron.py-2.px-1 p a {
        text-decoration: none;
    }
    .card {
        background-color: #ffffff;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
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
    .card-header {
        padding-bottom: 0; /* Menghilangkan jarak bawah pada header */
    }
    .table-responsive {
        margin-top: 10px; /* Menambahkan sedikit jarak antara ikon dan tabel */
    }
</style>
