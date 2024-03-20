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

            background-color: #b40000; /* Red background color */
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


        #user-name {
            display: inline-block; /* Ensures the name and button are on the same line */
        }

        .name-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .name-container p {
            margin-right: auto;
        }

        .edit-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }

        #new-name {
            display: block;
            margin-right: 10px;
            padding: 8px;
        }

        #change-name-form div {
            margin-left: auto;
        }

        .edit-container {
            display: flex;
            text-align: right;
        }


        @media (max-width: 768px) {

            #user-name {
                font-size: 1.5vh;
            }

            #new-name {
                font-size: 1.5vh;
                padding: 6px;
            }


            .align-content-end {
                display: flex;
                justify-content: flex-end;
                align-items: center;
            }


            .edit-button {
                padding: 6px 12px;
                font-size: 14px;

            }

            .inputtext {
                width: 100%;
            }
        }

        @media (max-width: 425px) {

            .inputtext {
                width: 100%;
            }
        }

        .confirmation-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* semi-transparent background */
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999; /* ensure it's above other content */
        }

        .confirmation-message {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }


        /* Add this CSS to your stylesheet */
        .btn-danger {
            background-color: #dc3545; /* Red background color */
            border-color: #dc3545; /* Red border color */
        }

        .btn-danger:hover,
        .btn-danger:focus {
            background-color: #c82333; /* Darker red background color on hover/focus */
            border-color: #c82333; /* Darker red border color on hover/focus */
        }

        .btn-danger:active {
            background-color: #bd2130; /* Even darker red background color on click */
            border-color: #bd2130; /* Even darker red border color on click */
        }


    </style>
</head>

<body>

<div class="container-fluid">

            <div class="card profile-header text-center mb-2">
                <div class="profile-image">
                    <!-- Add your profile image here -->
                    <img src="{{ asset('img/profile/profiledefault.png') }}" class="img-fluid rounded-circle" alt="Profile Image">
                </div>
                <div class="card-body">
                    <h4 class="mb-3">User Profile</h4>
                    <p class="mb-0">Welcome, {{ $user->name }}</p>
                    <p class="mb-0">Member since: {{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif



            <div class="card profile-info">
                <div class="card-body">
                    <h4>User Information</h4>
                    <div class="row">

                        <div class="col-md-6">

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Name</h5>
                                </div>
                                <div class="card-body" style="height: 8vh;">
                                    <div class="name-container">

                                        <form action="{{ route('change.name') }}" method="POST" id="change-name-form" style="display: none;">

                                            @csrf
                                            <div class="edit-container" style="display: flex; justify-content: flex-end; width: 100%;">

                                                <input class="inputtext" type="text" id="new-name" name="new-name" value="{{ $user->name }}">

                                                <div class="align-content-end">
                                                    <button class="edit-button mr-2" type="button" onclick="cancelEdit()">Cancel</button>
                                                    <button class="edit-button" type="submit">Save</button>
                                                </div>

                                            </div>

                                        </form>

                                        <p id="user-name" class="mt-2 ml-2" style="font-size: 1.8vh">{{ $user->name }}</p>
                                        <button class="edit-button" id="edit-btn" onclick="toggleEditForm()">Edit</button>

                                    </div>

                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Email</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $user->email }}</p>
                                </div>
                            </div>


                            @if($user->name != 'Administrator')

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Change Password</h5>
                                    </div>
                                    <div class="card-body">

                                        @if(session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        <form action="{{ route('change.password') }}" method="POST" id="change-password-form">
                                            @csrf

                                            <div class="form-group">
                                                <label for="current-password">Current Password</label>
                                                <input type="password" class="form-control" id="current-password" name="current-password" required>
                                                @error('current-password')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="new-password">New Password</label>
                                                <input type="password" class="form-control" id="new-password" name="new-password" required>
                                                @error('new-password')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="confirm-password">Confirm New Password</label>
                                                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                                                @error('confirm-password')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <button type="submit" class="btn btn-primary">Change Password</button>

                                        </form>

                                    </div>
                                </div>

                            @endif

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


                            @if($user->name != 'Administrator')

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Email Verification</h5>
                                    </div>
                                    <div class="card-body">


                                        @if (!$user->hasVerifiedEmail())
                                            <p class="mb-2">
                                                <i class="fa fa-exclamation-circle text-danger"></i> <!-- Font Awesome icon -->
                                                {{ __('Please verify your email address.') }}
                                            </p>

                                            <p class="mt-4 mb-1">
                                                {{ __('If you did not receive the email') }}
                                            </p>

                                            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">{{ __('Resend Verification Email') }}</button>
                                            </form>
                                        @else
                                            <p class="text-success mb-0">{{ __('Your email has been verified.') }}</p>
                                        @endif
                                    </div>
                                </div>

                            @endif




                            <!-- delete profile -->
                            <div class="card">

                                <div class="card-header" style="background-color: #b40000; color: white">
                                    <h5 class="mb-0">Delete Profile</h5>
                                </div>

                                <div class="card-body">

                                    <button type="button" class="btn btn-danger" onclick="showConfirmation()">Delete Profile</button>

                                    <div id="confirmationContainer" class="confirmation-container">
                                        <div class="confirmation-message">
                                            <p>Are you sure you want to delete your profile?</p>
                                            <button type="submit" class="btn btn-danger" onclick="submitForm()">Confirm</button>
                                            <button type="button" class="btn btn-secondary" onclick="hideConfirmation()">Cancel</button>
                                        </div>
                                    </div>

                                    <form id="deleteForm" action="{{ route('profile.delete', ['user' => auth()->user()->id]) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>



</body>

<script>

    function showConfirmation() {
        var confirmationContainer = document.getElementById('confirmationContainer');
        confirmationContainer.style.display = 'flex'; // display confirmation container
    }

    function hideConfirmation() {
        var confirmationContainer = document.getElementById('confirmationContainer');
        confirmationContainer.style.display = 'none'; // hide confirmation container
    }

    function submitForm() {
        var form = document.getElementById('deleteForm');
        form.submit(); // submit the form
    }


    function toggleEditForm() {

        // Get elements
        var editButton = document.getElementById('edit-btn');
        var changeNameForm = document.getElementById('change-name-form');
        var nameParagraph = document.getElementById('user-name');

        // Toggle visibility
        if (changeNameForm.style.display === 'none' || changeNameForm.style.display === '') {
            // Hide name paragraph and show change name form
            nameParagraph.style.display = 'none';
            changeNameForm.style.display = 'block';

            // Hide the edit button
            editButton.style.display = 'none';

        } else {

            // Show name paragraph and hide change name form
            nameParagraph.style.display = 'block';
            changeNameForm.style.display = 'none';

            // Show the edit button again
            editButton.style.display = 'block';

        }

    }


    function cancelEdit() {

        // Get elements
        var editButton = document.getElementById('edit-btn');
        var changeNameForm = document.getElementById('change-name-form');
        var nameParagraph = document.getElementById('user-name');

        // Hide the change name form and show the name paragraph
        nameParagraph.style.display = 'block';
        changeNameForm.style.display = 'none';

        // Show the edit button again
        editButton.style.display = 'block';

        // Set the text of the edit button back to 'Edit'
        editButton.innerText = 'Edit';

    }


    // Media Query Script

    window.addEventListener('resize', function() {

        if (window.innerWidth <= 768) {

            // Get all elements with class col-md-6
            var cols = document.querySelectorAll('.col-md-6');
            // Loop through each element and change its class to col-md-12
            cols.forEach(function(col) {
                col.classList.remove('col-md-6');
                col.classList.add('col-md-12');
            });

        } else {

            // If screen width is larger than or equal to 768px, revert the changes
            var cols = document.querySelectorAll('.col-md-12');
            cols.forEach(function(col) {
                col.classList.remove('col-md-12');
                col.classList.add('col-md-6');
            });

        }
    });

    // Trigger the resize event on page load to apply the initial changes
    window.dispatchEvent(new Event('resize'));

</script>

</html>

</script>

</html>


@endsection
