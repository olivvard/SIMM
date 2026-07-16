<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.head-css')
</head>

<body class="container-fluid">

    <div class="row">
        <div class="col-xl-7"><img class="bg-img-cover bg-center" src="{{ asset('assets/images/login.jpg') }}" alt="looginpage">
        </div>
        <div class="col-xl-5 p-0">
            <div class="login-card">
                <form class="theme-form login-form" method="POST" action="{{ route('login.submit') }}" novalidate>
                    @csrf
                    <h4>Login</h4>
                    <h6>Welcome back! Log in to your account.</h6>
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <div class="form-group">
                        <label>Username</label>
                        <div class="input-group"><span class="input-group-text"><i class="icon-user"></i></span>
                            <input class="form-control @error('username') is-invalid @enderror" type="text"
                                name="username" value="{{ old('username') }}" required="" placeholder="input username">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group"><span class="input-group-text"><i class="icon-lock"></i></span>
                            <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" required=""
                                placeholder="*********">
                            <div class="show-hide"><span class="show"> </span></div>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" type="submit">Sign in</button>
                    </div>
                    <p>Don't have account?<a class="ms-2" href="{{ route('register.show') }}">Create Account</a></p>
                </form>
            </div>
        </div>
    </div>

    @include('components.vendor')
</body>

</html>