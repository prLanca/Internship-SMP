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

    <style>

        .userstable {
            height: 78vh;
        }

        .table-container {
            height: 60vh;
            overflow-y: auto;
            position: relative;
        }

        .table-container::-webkit-scrollbar {
            width: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Media query for screens with a maximum width of 768px */
        @media (max-width: 768px) {
            .button-container {

                width: auto;
                display: flex;
                gap: 8px;
            }

        }

        /* Media query for screen widths between 425px and 768px */
        @media (min-width: 426px) and (max-width: 768px) {

            .cardname {
                font-size: 0.7rem;
                margin-top: 0.6rem;
            }

            .cardcount {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 0.5rem;
            }

        }

        /* Media query for screen widths between 768px and 1024px */
        @media (min-width: 769px) and (max-width: 1024px) {

            .cardname {
                font-size: 0.8rem;
                margin-top: 0.8rem;
            }

            .cardcount {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 0.6rem;
            }

        }

    </style>

</head>

<body>

<!-- ############################## Dashboard ############################## -->

<div class="container-fluid mt-4">

    <div class="card align-items">

        <div class="card-header bg-danger text-white text-center">
            <h4>Admin Dashboard</h4>
        </div>

        <!-- ######################################## Qty. Files ######################################## -->

        <div class="row mt-2" style="padding: 2vh 4vh 0 4vh ;">
            @foreach($fileInfo as $directory => $info)
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="cardimage">
                                    <img src="{{ asset('img/dashboard/infofile.png') }}" alt="Custom Icon" class="custom-icon" style="height: 50px;">
                                </div>
                                <div>
                                    <h6 class="mb-0 cardname" style="">{{ $directory }}</h6>
                                    <h3 class="font-weight-bold cardcount">{{ $info['file_count'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($loop->iteration % 4 == 0 || $loop->last)

                    @php
                        $bottomPadding = ($loop->last) ? '0' : '2vh';
                    @endphp

        </div>

        @if(!$loop->last)

            <div class="row" style="padding: 0 4vh;">

        @endif

                @endif

            @endforeach

        <!-- ###################################### End Qty. Files ###################################### -->

        <!-- ########################################## Users ########################################## -->

        <div class="card-body userstable">

            <div class="row">

                <div class="col-md-12">

                    <!-- ########################################## Filter by role ########################################## -->

                    <div class="mb-4">
                        <select id="role-filter" class="form-control">
                            <option value="all">All</option>
                            <option value="admin">Admin</option>
                            <option value="worker">Worker</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>

                    <!-- ######################################## End Filter by role ######################################## -->

                    <!-- ########################################## users tables ########################################## -->

                    @if(isset($users))

                        <div class="table-container">

                            <div class="table-responsive">

                                <!-- ########################################## All users table ########################################## -->

                                <table id="all-users-table" class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <form action="{{ route('admin.users.update') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                                <td>{{$user->id}}</td>
                                                <td><input type="text" class="form-control" name="name" value="{{$user->name}}" readonly></td>
                                                <td><input type="email" class="form-control" name="email" value="{{$user->email}}" readonly></td>
                                                <td>
                                                    <select class="form-control role-select" name="role" @if(!empty($user->id)) disabled @endif>
                                                        <option value="admin" @if($user->hasRole('admin')) selected @endif>Admin</option>
                                                        <option value="worker" @if($user->hasRole('worker')) selected @endif>Worker</option>
                                                        <option value="viewer" @if($user->hasRole('viewer')) selected @endif>Viewer</option>
                                                    </select>
                                                </td>
                                                <td class="button-container">
                                                    <button type="button" class="btn btn-success edit-btn" data-target="role-select"><i class="fas fa-edit"></i></button>
                                                    <button type="submit" class="btn btn-primary save-btn" style="display: none;"><i class="fa fa-save"></i></button>

                                            </form>
                                            <form action="{{ route('admin.users.delete', ['userid' => $user->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <!-- ########################################## End All users table ########################################## -->

                                <!-- ########################################## Admin users table ########################################## -->

                                <table id="admin-table" class="table table-hover">
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
                                            <form action="{{ route('admin.users.update') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                                <td>{{$user->id}}</td>
                                                <td><input type="text" class="form-control" name="name" value="{{$user->name}}" readonly></td>
                                                <td><input type="email" class="form-control" name="email" value="{{$user->email}}" readonly></td>
                                                <td>
                                                    <select class="form-control role-select" name="role" @if(!empty($user->id)) disabled @endif>
                                                        <option value="admin" @if($user->hasRole('admin')) selected @endif>Admin</option>
                                                        <option value="worker" @if($user->hasRole('worker')) selected @endif>Worker</option>
                                                        <option value="viewer" @if($user->hasRole('viewer')) selected @endif>Viewer</option>
                                                    </select>
                                                </td>
                                                <td class="button-container">
                                                    <button type="button" class="btn btn-success edit-btn" data-target="role-select"><i class="fas fa-edit"></i></button>
                                                    <button type="submit" class="btn btn-primary save-btn" style="display: none;"><i class="fa fa-save"></i></button>

                                            </form>
                                            <form action="{{ route('admin.users.delete', ['userid' => $user->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <!-- ########################################## End Admin users table ########################################## -->

                                <!-- ########################################## Worker users table ########################################## -->

                                <table id="worker-table" class="table table-hover">
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
                                            <form action="{{ route('admin.users.update') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                                <td>{{$user->id}}</td>
                                                <td><input type="text" class="form-control" name="name" value="{{$user->name}}" readonly></td>
                                                <td><input type="email" class="form-control" name="email" value="{{$user->email}}" readonly></td>
                                                <td>
                                                    <select class="form-control role-select" name="role" @if(!empty($user->id)) disabled @endif>
                                                        <option value="admin" @if($user->hasRole('admin')) selected @endif>Admin</option>
                                                        <option value="worker" @if($user->hasRole('worker')) selected @endif>Worker</option>
                                                        <option value="viewer" @if($user->hasRole('viewer')) selected @endif>Viewer</option>
                                                    </select>
                                                </td>
                                                <td class="button-container">
                                                    <button type="button" class="btn btn-success edit-btn" data-target="role-select"><i class="fas fa-edit"></i></button>
                                                    <button type="submit" class="btn btn-primary save-btn" style="display: none;"><i class="fa fa-save"></i></button>

                                            </form>
                                            <form action="{{ route('admin.users.delete', ['userid' => $user->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <!-- ########################################## End Worker users table ########################################## -->

                                <!-- ########################################## Viewer users table ########################################## -->

                                <table id="viewer-table" class="table table-hover">
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
                                            <form action="{{ route('admin.users.update') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                                <td>{{$user->id}}</td>
                                                <td><input type="text" class="form-control" name="name" value="{{$user->name}}" readonly></td>
                                                <td><input type="email" class="form-control" name="email" value="{{$user->email}}" readonly></td>
                                                <td>
                                                    <select class="form-control role-select" name="role" @if(!empty($user->id)) disabled @endif>
                                                        <option value="admin" @if($user->hasRole('admin')) selected @endif>Admin</option>
                                                        <option value="worker" @if($user->hasRole('worker')) selected @endif>Worker</option>
                                                        <option value="viewer" @if($user->hasRole('viewer')) selected @endif>Viewer</option>
                                                    </select>
                                                </td>
                                                <td class="button-container">
                                                    <button type="button" class="btn btn-success edit-btn" data-target="role-select"><i class="fas fa-edit"></i></button>
                                                    <button type="submit" class="btn btn-primary save-btn" style="display: none;"><i class="fa fa-save"></i></button>

                                            </form>
                                            <form action="{{ route('admin.users.delete', ['userid' => $user->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </tr>
                                    @endforeach

                                    </tbody>

                                </table>

                                <!-- ########################################## End Viewer users table ########################################## -->

                            </div>

                        </div>

                    @else
                        <p>No users found</p>
                    @endif

                    <!-- ########################################## End users tables ########################################## -->

                </div>

            </div>

        </div>

        <!-- ######################################## End Users ######################################## -->

    </div>

    </div>

</div>



<script>

    /* ########################################## Button edit ########################################## */

    // JavaScript to handle edit button click event
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            row.querySelectorAll('input, select').forEach(field => {
                field.removeAttribute('readonly');
            });

            const targetId = button.getAttribute('data-target');
            const selects = row.querySelectorAll('select');

            selects.forEach(select => {
                select.removeAttribute('disabled');
            });

            row.querySelector('.edit-btn').style.display = 'none';
            row.querySelector('.save-btn').style.display = 'inline-block';
        });
    });

    /* ########################################## End Button edit ########################################## */

    /* ########################################## Button save ########################################## */

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

                    if (data && data.success) {

                        alert('User updated successfully');

                        window.location.href = '/dashboard';
                    }
                })
                .catch(error => {

                    console.error(error);

                });
        });
    });

    /* ########################################## End Button save ########################################## */

    /* ########################################## Filter by role ########################################## */

    function filterUsersByRole(role) {

        document.querySelectorAll('.table').forEach(table => {
            table.style.display = 'none';
        });


        document.querySelectorAll('.card-subtitle').forEach(subtitle => {
            subtitle.style.display = 'none';
        });


        if (role === 'all') {

            document.getElementById('all-users-table').style.display = 'table';
            document.getElementById('pagination-allusers').style.display = 'block';

        } else if (role === 'admin') {

            document.getElementById('admin-table').style.display = 'table';
            document.getElementById('admin-subtitle').style.display = 'block';

        } else if (role === 'worker') {

            document.getElementById('worker-table').style.display = 'table';
            document.getElementById('worker-subtitle').style.display = 'block';

        } else if (role === 'viewer') {

            document.getElementById('viewer-table').style.display = 'table';
            document.getElementById('viewer-subtitle').style.display = 'block';

        }
    }

    document.getElementById('role-filter').addEventListener('change', function() {
        var role = this.value;
        filterUsersByRole(role);
    });

    filterUsersByRole('all');

    /* ########################################## End Filter by role ########################################## */

    /* ########################################## Search users ########################################## */

    function filterUsers(searchQuery) {
        const rows = document.querySelectorAll('#all-users-table tbody tr');
        rows.forEach(row => {
            const name = row.querySelector('td').textContent.trim().toLowerCase();
            if (name.includes(searchQuery)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    document.getElementById('search-input-users').addEventListener('input', function() {
        const searchQuery = this.value.trim();
        console.log(searchQuery);
    });

    /* ########################################## End Search users ########################################## */

</script>

</body>

@endsection
