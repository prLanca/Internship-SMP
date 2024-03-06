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

        /* Adjust margins for the buttons */
        #change-name-form div {
            margin-left: auto;
        }

        .edit-container {
            display: flex;
            text-align: right;
        }


        @media (max-width: 768px) {
            /* Adjust font size for smaller screens */
            #user-name {
                font-size: 1.5vh;
            }

            #new-name {
                font-size: 1.5vh;
                padding: 6px;
            }

            /* Adjust layout for smaller screens */
            .align-content-end {
                display: flex;
                justify-content: flex-end;
                align-items: center;
            }

            /* Adjust button size for smaller screens */
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


                        <div class="col-md-12">

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Change Password</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('change.password') }}" method="POST" id="change-password-form">
                                        @csrf

                                        @if(session('success'))
                                            <div class="alert alert-success">{{ session('success') }}</div>
                                        @endif


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

                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

<script>

    function toggleEditForm() {
        // Get elements
        var editButton = document.getElementById('edit-btn'); // Changed to get by id
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
        var editButton = document.getElementById('edit-btn'); // Changed to get by id
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
