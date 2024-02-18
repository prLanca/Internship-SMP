
<!DOCTYPE html>
<html>

<head>

    <title>Motherson</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 p-2">
            <div class="card">
                <div class="card-header bg-danger text-white text-center">
                    <h4>Sign in to start your session</h4>
                </div>
                <div class="card-body">

                    <form method="POST" action="{{ route('register') }}">

                        @csrf
                        <div class="mb-3">
                            <label for="exampleNome" class="form-label">Name</label>
                            <input type="text" class="form-control rounded-pill @error('name') is-invalid @enderror" name="name" id="exampleNome" placeholder="Name" value="{{ old('name') }}">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Email address</label>
                            <input type="email" class="form-control rounded-pill @error('email') is-invalid @enderror" name="email" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" value="{{ old('email') }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control rounded-pill @error('password') is-invalid @enderror" id="exampleInputPassword1" placeholder="Password">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="exampleConfirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-pill" id="exampleInputPassword1" placeholder="Confirm Password">
                        </div>

                        <button type="submit" class="btn btn-danger btn-block rounded-pill">Register</button>

                        <div class="mt-3 text-center">
                            <p>Already have an account? <a href="{{route('login')}}">Login</a></p>
                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>


