@extends('adminlte::page')

@section('title', 'Profile')

@section('content_header')
    <div class="jumbotron py-2 px-1 bg-light text-center mb-4">
        <br>
        <h1 style="color: #343a40;">
            Profile
        </h1>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body text-center"> <!-- Tambahkan text-center untuk memusatkan konten -->
            <!-- Menampilkan pesan sukses -->
            @if(session('success'))
                <div class="alert alert-info">
                    {{ session('success') }}
                </div>
            @endif
            
            <!-- Menampilkan gambar profil -->
            @if(Auth::user()->profile_picture)
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px;">
            @else
                <img src="{{ asset('path/to/default/profile/picture.png') }}" alt="Default Profile Picture" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px;">
            @endif
            <h3>{{ Auth::user()->name }}</h3>
            <p>Email: {{ Auth::user()->email }}</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-secondary">Edit Profil</a>
            <a href="{{ route('profile.change_password') }}" class="btn btn-primary">Change Password</a>
            <!-- Tambahkan informasi lain sesuai kebutuhan -->
        </div>
    </div>
@endsection
