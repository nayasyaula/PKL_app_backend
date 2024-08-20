@extends('adminlte::page')

@section('content')
    <div class="jumbotron py-2 px-1 bg-light text-center mb-4">
        <br>
        <h1 style="color: #343a40;">
            Input Today's Attendance
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

    <a href="{{ route('word.attendance') }}" class="btn btn-primary mt-3">Expor Jurnal Kehadiran</a>
<!-- Pastikan $userId didefinisikan di view ini -->
{{-- <a href="{{ route('somepage') }}" class="button">Generate QR Code</a> --}}
<a href="{{ route('generate-qr') }}" class="btn btn-secondary mt-3">Generate QR Code</a> <!-- Tombol baru untuk QR Code -->

    <br>

    <div class="table-responsive">
        <br>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Attendance IN</th>
                    <th scope="col">Attendance OUT</th>
                    <th scope="col">Status Now</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($attendance as $item)
                    <tr>
                        <th scope="row">{{ $no++ }}</th>
                        <td>{{ $item->in }}</td>
                        <td>{{ $item->out }}</td>
                        <td>{{ $item->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
</style>
