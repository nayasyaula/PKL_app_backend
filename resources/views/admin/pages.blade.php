@extends('adminlte::page')

@section('content')
    <div class="jumbotron py-2 px-1 bg-light">
        <h4 class="display-4" style="color: black;">
            Input Kehadiran Hari ini
        </h4>
    </div>
    <form action="{{ route('admin.store')}}" method="POST" class="card p-5 shadow border-0 mx-auto" style="width: 80%">
        @csrf
        <input type="hidden" name="in" id="inField">
        <input type="hidden" name="out" id="outField">
        <button type="submit" class="btn btn-primary mt-3" id="attendanceButton">Masuk</button>
    </form>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const attendanceButton = document.getElementById('attendanceButton');
            const inField = document.getElementById('inField');
            const outField = document.getElementById('outField');

            attendanceButton.addEventListener('click', function (e) {
                e.preventDefault(); // Prevent form submission

                if (attendanceButton.textContent === 'Masuk') {
                    inField.value = new Date().toISOString(); // Set 'in' value
                    attendanceButton.textContent = 'Keluar';
                    attendanceButton.classList.remove('btn-primary');
                    attendanceButton.classList.add('btn-danger');
                } else {
                    outField.value = new Date().toISOString(); // Set 'out' value
                    attendanceButton.textContent = 'Masuk';
                    attendanceButton.classList.remove('btn-danger');
                    attendanceButton.classList.add('btn-primary');
                }

                // Submit the form after setting the values
                e.target.form.submit();
            });
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
</style>
