@extends('layouts.main')
@section('content')

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

<!-- dashboard -->
<div class="container-fluid mt-4"> <!-- Use container-fluid to fill the entire width -->

    <div class="card">
        <div class="card-header bg-danger text-white text-center">
            <h4>Admin Dashboard</h4>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-header bg-danger text-white text-center">
                            <h4>Menu</h4>
                        </div>
                        <div class="card-body">

                            <ul class="list-group list-group-flush">

                                <li class="list-group list-group-item">

                                    <a href="">Users</a>

                                </li>

                            </ul>

                        </div>

                    </div>

                </div>

                <div class="col-md-9">

                    <!-- Dropdown menu for selecting role -->
                    <div class="mb-4">
                        <select id="role-filter" class="form-control">
                            <option value="all">All</option>
                            <option value="admin">Admin</option>
                            <option value="worker">Worker</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>


                    @if(isset($users))

                        <!-- sub-title -->
                        <h4 class="card-subtitle mb-1 text-muted fs-5">Administrators</h4>

                        <!-- Admin Users Table -->
                        <table id="admin-table" class="table table-bordered table-hover table-warning">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->filter(function($user) { return $user->hasRole('admin') && $user->id != Auth::id(); }) as $adminUser)
                                <tr>
                                    <form action="{{ route('admin.users.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="user_id" value="{{$adminUser->id}}">
                                        <td>{{$adminUser->id}}</td>
                                        <td><input type="text" class="form-control" name="name" value="{{$adminUser->name}}" readonly></td>
                                        <td><input type="email" class="form-control" name="email" value="{{$adminUser->email}}" readonly></td>
                                        <td>
                                            <select class="form-control role-select" name="role" @if(!empty($adminUser->id)) disabled @endif>
                                                <option value="admin" @if($adminUser->hasRole('admin')) selected @endif>Admin</option>
                                                <option value="worker" @if($adminUser->hasRole('worker')) selected @endif>Worker</option>
                                                <option value="viewer" @if(isset($adminUser) && (!$adminUser->hasRole('admin') && !$user->hasRole('worker'))) selected @endif>Viewer</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success edit-btn" data-target="role-select"><i class="fas fa-edit"></i></button>
                                            <button type="submit" class="btn btn-primary save-btn" style="display: none;"><i class="fa fa-save"></i></button>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <!-- sub-title -->
                        <h4 class="card-subtitle mb-1 text-muted fs-5">Workers</h4>

                        <!-- Worker Users Table -->
                        <table id="worker-table" class="table table-bordered table-hover table-primary">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->filter(function($user) { return $user->hasRole('worker') && $user->id != Auth::id(); }) as $workerUser)
                                <tr>
                                    <form action="{{ route('admin.users.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="user_id" value="{{$workerUser->id}}">
                                        <td>{{$workerUser->id}}</td>
                                        <td><input type="text" class="form-control" name="name" value="{{$workerUser->name}}" readonly></td>
                                        <td><input type="email" class="form-control" name="email" value="{{$workerUser->email}}" readonly></td>
                                        <td>
                                            <select class="form-control role-select" name="role" @if(!empty($workerUser->id)) disabled @endif>
                                                <option value="admin" @if($workerUser->hasRole('admin')) selected @endif>Admin</option>
                                                <option value="worker" @if($workerUser->hasRole('worker')) selected @endif>Worker</option>
                                                <option value="viewer" @if(isset($workerUser) && (!$workerUser->hasRole('admin') && !$workerUser->hasRole('worker'))) selected @endif>Viewer</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success edit-btn" data-target="role-select"><i class="fas fa-edit"></i></button>
                                            <button type="submit" class="btn btn-primary save-btn" style="display: none;"><i class="fa fa-save"></i></button>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <!-- sub-title -->
                        <h4 class="card-subtitle mb-1 text-muted fs-5">Viewers</h4>

                        <!-- Viewer Users Table -->
                        <table id="viewer-table" class="table table-bordered table-hover table-success">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->filter(function($user) { return $user->hasRole('viewer') && $user->id != Auth::id(); }) as $viewerUser)
                                <tr>
                                    <form action="{{ route('admin.users.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="user_id" value="{{$viewerUser->id}}">
                                        <td>{{$viewerUser->id}}</td>
                                        <td><input type="text" class="form-control" name="name" value="{{$viewerUser->name}}" readonly></td>
                                        <td><input type="email" class="form-control" name="email" value="{{$viewerUser->email}}" readonly></td>
                                        <td>
                                            <select class="form-control role-select" name="role" @if(!empty($viewerUser->id)) disabled @endif>
                                                <option value="admin" @if($viewerUser->hasRole('admin')) selected @endif>Admin</option>
                                                <option value="worker" @if($viewerUser->hasRole('worker')) selected @endif>Worker</option>
                                                <option value="viewer" @if(isset($viewerUser) && (!$viewerUser->hasRole('admin') && !$viewerUser->hasRole('worker'))) selected @endif>Viewer</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success edit-btn" data-target="role-select"><i class="fas fa-edit"></i></button>
                                            <button type="submit" class="btn btn-primary save-btn" style="display: none;"><i class="fa fa-save"></i></button>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    @else
                        <p>No users found</p>
                    @endif


                </div>

            </div>

        </div>

    </div>

</div>

<script>
    // JavaScript to handle edit button click event
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            row.querySelectorAll('input, select').forEach(field => {
                field.removeAttribute('readonly');
            });

            const targetId = button.getAttribute('data-target');
            const select = document.querySelector(`.${targetId}`);
            select.removeAttribute('disabled');



            row.querySelector('.edit-btn').style.display = 'none';
            row.querySelector('.save-btn').style.display = 'inline-block';
        });
    });


    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            const form = row.querySelector('form');
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'PUT',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to update user');
                    }
                    return response.json();
                })
                .then(data => {
                    // Check if the update was successful
                    if (data && data.success) {
                        // Optionally, update the UI to reflect changes
                        alert('User updated successfully');
                        // Redirect to the dashboard page
                        window.location.href = '/dashboard';
                    }
                })
                .catch(error => {
                    // Handle error
                    console.error(error);

                });
        });
    });

    function filterUsersByRole(role) {
        // Hide all tables
        document.querySelectorAll('.table').forEach(table => {
            table.style.display = 'none';
        });

        // Hide all subtitles
        document.querySelectorAll('.card-subtitle').forEach(subtitle => {
            subtitle.style.display = 'none';
        });

        // Show tables based on selected role
        if (role === 'all') {
            // Show all tables
            document.querySelectorAll('.table').forEach(table => {
                table.style.display = 'table';
            });
            // Show all subtitles
            document.querySelectorAll('.card-subtitle').forEach(subtitle => {
                subtitle.style.display = 'block';
            });
        } else {
            // Show table corresponding to the selected role
            document.getElementById(`${role}-table`).style.display = 'table';
        }
    }

    // Event listener for role filter dropdown
    document.getElementById('role-filter').addEventListener('change', function() {
        var role = this.value;
        filterUsersByRole(role);
    });

    // Show all tables initially
    filterUsersByRole('all');



</script>

</body>

@endsection
