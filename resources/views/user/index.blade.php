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

<!-- profile -->
<div class="container-fluid mt-4"> <!-- Use container-fluid to fill the entire width -->

    <div class="card">
        <div class="card-header bg-danger text-white text-center">
            <h4>User Profile</h4>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header bg-danger text-white text-center">
                            <h4>Menu</h4>
                        </div>
                        <div class="card-body">

                            <ul class="list-group
                                    list-group-flush">

                                <li class="list-group
                                        list-group-item">

                                    <a href="">Profile</a>

                                </li>

                                <li class="list-group

                                        list-group-item">

                                    <a href="">Change Password</a>

                                </li>

                            </ul>

                        </div>

                    </div>

                </div>

                <div class="col-md-9">

                    <form>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Role</label>
                            <div class="col-md-6">

                                @if($user->hasRole('admin'))

                                    <input id="email" type="email" class="form-control" name="email" value="Administrator" readonly>

                                @elseif($user->hasRole('user'))

                                    <input id="email" type="email" class="form-control" name="email" value="User" readonly>

                                @else

                                    <input id="email" type="email" class="form-control" name="email" value="Worker" readonly>

                                @endif

                            </div>
                        </div>

                    </form>


                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>




@endsection


