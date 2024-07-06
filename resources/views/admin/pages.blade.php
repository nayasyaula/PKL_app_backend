@extends('adminlte:page')

@section('content')
        <div class="jumbroton py-2 px-1">
            <h4 class="display" style="color: black;">
                Tambah Data Rayon
            </h4>
            <p style="color: black;">
                <a class="button_waterpump" style="color: black;" href="/admin/dashboard">Home</a> /
                <a class="button_waterpump" style="color: black;" href="{{ route('admin.rayon.home') }}">Data Rayon</a> /
                <a class="button_waterpump" style="color: black;">Tambah Data Rayon</a>
            </p>
        </div>
        @if (Session::get('success'))
            <div class="alert alert-success" {{ Session::get('success') }}></div>
        @endif
        @if ($errors->any())
            <ul class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    <form action="{{ route('admin.rayon.store') }}" method="POST" class="card p-5 shadow p-1 mb-4 border-0" style="width: 80%">
        @csrf
        <div class="mb-3 row">
            <label for="rayon" class="col-sm-2 col-form-label">Rayon</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="rayon" name="rayon">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="price" class="col-sm-2 col-form-label">Pembimbing Siswa</label>
            <div class="col-sm-10">
                <select name="user_id" class="form-control form-control-select">
                    @foreach($users as $user)
                    @if($user['role'] == 'ps')
                    <option selected disabled hidden>Pilih</option>
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Tambah</button>
    </form>
@endsection

<style>
    .jumbroton.py-2.px-1 p a {
        text-decoration: none;
    }
</style>