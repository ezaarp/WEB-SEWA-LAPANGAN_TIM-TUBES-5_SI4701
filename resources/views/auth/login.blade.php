@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3>Login</h3>
                        <p class="text-muted">Sistem Peminjaman Fasilitas Kampus</p>
                    </div>

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus
                                   placeholder="Masukkan email Anda">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required
                                   placeholder="Masukkan password Anda">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            Login
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="text-muted mb-0">Belum punya akun?</p>
                        <a href="{{ route('register') }}" class="text-decoration-none">
                            Daftar sekarang
                        </a>
                    </div>

                    <hr class="my-4">
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <strong>Akun Demo:</strong><br>
                            <strong>Admin:</strong> admin@telkomuniversity.ac.id<br>
                            <strong>PJ:</strong> pj@telkomuniversity.ac.id<br>
                            <strong>Mahasiswa:</strong> mahasiswa@student.telkomuniversity.ac.id<br>
                            <strong>Password:</strong> password
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 