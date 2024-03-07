<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motherson Portal</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 120vh;
            text-align: center;
            margin: 1vh;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
            font-size: 28px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            width: 100%;
            text-align: left;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        button[type="submit"] {
            width: 80vh;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button[type="submit"] {
            background-color: #b40000;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 18px;
        }

        button[type="submit"]:hover {
            background-color: #730000;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            button[type="submit"] {
                padding: 10px;
                font-size: 14px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            button[type="submit"] {
                width: 60vh;
            }
        }

        @media (max-width: 576px) {
            input[type="text"],
            input[type="email"],
            input[type="password"],
            button[type="submit"] {
                width: 40vh;
            }
        }

        @media (max-width: 420px) {
            input[type="text"],
            input[type="email"],
            input[type="password"],
            button[type="submit"] {
                width: 30vh;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Forgot the Password</h1>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Input -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email address') }}</label>
            <input type="email" name="email" id="email" class="form-control rounded-pill @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="{{ __('Enter email') }}">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Button -->
        <div class="text-center position-relative">
            <button type="submit" class="btn btn-danger btn-block rounded-pill">{{ __('Send Password Reset Link') }}</button>
        </div>

    </form>

    <div class="text-center mt-3">
        Already have an account? <a href="{{ route('login') }}">Login</a>
    </div>

</div>

</body>

</html>
