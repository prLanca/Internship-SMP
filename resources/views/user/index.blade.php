@extends('layouts.main')
@section('content')

    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* Custom styles */
        .profile-header {
            background-color: #dc3545; /* Red background color */
            color: #fff;
            padding: 20px;
        }

        .profile-info {
            margin-top: 20px;
        }

        .profile-info h4 {
            margin-bottom: 20px;
        }

        .profile-info .card {
            border: none;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-info .card-header {
            background-color: #f8f9fa;
            border-bottom: none;
            padding: 15px;
        }

        .profile-info .card-body {
            padding: 15px;
        }


        .profile-header {
            background-color: #b40000; /* Blue background color */
            color: #fff;
            padding: 20px;
        }

        .profile-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #ffffff; /* White border around the image */
            margin-bottom: 15px;
        }

        .profile-image img:hover {
            filter: brightness(90%); /* Reduce brightness on hover for a subtle effect */
        }

    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">


        <div class="col-md-3">
            <div class="card profile-header text-center">
                <div class="profile-image">
                    <!-- Add your profile image here -->
                    <img src="{{ asset('img/profile/profiledefault.png') }}" class="img-fluid rounded-circle" alt="Profile Image">
                </div>
                <div class="card-body">
                    <h4 class="mb-3">User Profile</h4>
                    <p class="mb-0">Welcome, {{ $user->name }}</p>
                </div>
            </div>
        </div>



        <div class="col-md-9">
            <div class="card profile-info">
                <div class="card-body">
                    <h4>User Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Name</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $user->name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Email</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Role</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $user->roles()->first()->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

</body>

</html>


@endsection
