@extends('adminlte::page')

@section('title', 'Edit Profile')

@section('content_header')
    <div class="jumbotron py-2 px-1 bg-light text-center mb-4">
        <br>
        <h1 style="color: #343a40;">
            Edit Profile
        </h1>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" required>
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <!-- Menampilkan gambar profil jika ada -->
                @if (Auth::user()->profile_picture)
                    <div class="mb-2">
                        <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="Profile Picture" style="max-width: 100px; height: auto;"/>
                    </div>
                @endif
                <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
                @error('profile_picture')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</div>
@endsection
