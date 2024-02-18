<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App</title>

    <style>

        /* Default styles for the sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 5%;
            background-color: #313131;
            transition: width 0.3s ease, left 0.3s ease; /* Transition for opening */
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            padding-bottom: 0;
        }


        .main-content {
            margin-left: 7%;
            padding: 20px;
            transition: margin-left 0.5s ease;
            width: calc(90% - 60px);
        }

        #sidebarToggle {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            transition: transform 0.3s ease; /* Adjust transition timing and easing */
        }

        #sidebarToggle i {
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .sidebar.open #sidebarToggle i {
            transform: scale(1.2);
        }

        .sidebar.open {
            width: 250px;
        }

        .main-content.open {
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        .sidebar.open .container {
            width: 100%; /* Add this line */
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
        }

        .sidebar.open .user-info {
            display: flex;
            align-items: center;
            height: 50px;
        }

        .user-avatar {
            margin-right: 10px;
        }

        .user-details {
            text-align: center;
        }

        .user-details h5 {
            margin-bottom: 2px;
            font-size: 0.9rem;
        }

        .user-details p {
            margin: 0;
            font-size: 0.8rem;
        }

        .logout {
            margin-left: auto;
        }

        .logout button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px 5px 10px;
            border-radius: 5px;
        }


        .sidebar:not(.open) .user-info,
        .sidebar:not(.open) .logout {
            display: none;
            margin-bottom: 18px;
        }

        .sidebar:not(.open) .logout {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding-top: 20px;
        }

        /* Additional styles for navigation buttons */
        .navigation {
            position: absolute;
            top: 20%;
            left: 5px; /* Adjusted left offset */
            transform: translateY(-50%);
            width: calc(100% - 10px); /* Adjusted width to account for the left offset */
        }

        .nav-button {
            position: relative;
            margin-bottom: 10px;
            text-align: center;
            background-color: #dc3545;
            border-radius: 5px;
            padding: 12px 40px 12px 30px; /* Adjusted padding for accommodating the icon */
            transition: background-color 0.3s, transform 0.5s ease 0.2s;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: 50px;
        }

        .dashboard-button {
            position: absolute;
            bottom: 30px;
            left: 5px; /* Adjusted left offset */
            transform: translateY(-50%);
            width: calc(100% - 10px); /* Adjusted width to account for the left offset */
        }

        .nav-button:hover {
            background-color: #c82333;
        }

        .nav-button span {
            position: absolute;
            left: 40%;
            transition: opacity 0.3s, transform 0.5s ease;
            white-space: nowrap;
            opacity: 0;
        }

        .sidebar.open .nav-button span {
            opacity: 1;
        }

        .sidebar:not(.open) .nav-button span {
            display: none; /* Hide the text when the sidebar is closed */
        }

        .nav-button a i {

            display: flex;
            align-items: center;
            color: inherit;
            text-decoration: none;

        }

        .sidebaricons {
            font-size: 1.4rem; /* Adjusted font size to make the icons bigger */
        }

        .sidebar:not(.open) .nav-button a i {

            position: static;
            left: 100%;

        }

        .bottom-bar {
            display: none;
        }

        /* Styles for the top bar */
        .top-bar {
            display: none;
        }


        /* TODO: Add media queries for 4k Screen (2560px) */

        @media (max-width: 1440px) {

            .nav-button {
                padding: 12px 40px 12px 18px; /* Adjusted padding for accommodating the icon */
            }

        }

        @media (max-width: 1024px){

            .sidebar {

                width: 7%;

            }

            .nav-button {

                padding: 12px 40px 12px 18px; /* Adjusted padding for accommodating the icon */

            }


        }

        /* Transform the sidebar into the bottom-bar */
        @media (max-width: 768px) {

            /* Hide the sidebar at the top */
            .sidebar {
                display: none;
            }

            /* Styles for the top bar */
            .top-bar {
                background-color: #313131;
                color: #fff;
                padding: 10px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .company-info {
                display: flex;
                align-items: center;
            }

            .company-logo img {
                width: 25px; /* Adjust width as needed */
                height: auto; /* Maintain aspect ratio */
                margin-right: 10px; /* Add some space between logo and name */
            }

            .company-name {
                font-size: 1rem;
            }

            /* Styles for the bottom bar */
            .bottom-bar {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 7vh; /* Adjust height as needed */
                background-color: #313131;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0 20px; /* Add padding to the left and right */
            }

            /* Styles for navigation buttons container */
            .nav-buttons-container {
                display: flex;
                justify-content: space-between; /* Distribute space evenly between buttons */
                flex-grow: 1; /* Allow container to grow and occupy available space */
            }

            /* Styles for navigation buttons */
            .nav-button {
                text-align: center;
                background-color: #313131;
                padding: 10px; /* Adjust padding as needed */
                transition: background-color 0.3s;
                display: flex;
                align-items: center;
                margin-bottom: 0;
            }


            /* Style for the last navigation button to remove the right margin */
            .nav-button:last-child {
                margin-right: 5%;
            }

            .nav-button:first-child {
                margin-left: 5%;
            }

            .nav-button:hover {
                background-color: #434343;
            }

            .nav-button span {
                margin-left: 8px; /* Adjust spacing between icon and text */
            }

            /* Styles for icons */
            .sidebaricons {
                font-size: 1.4rem; /* Adjusted font size to make the icons bigger */
                color: #fff; /* Icon color */
            }

            /* Style for logout button */
            .nav-button button {
                background-color: #dc3545;
                color: #fff;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
            }

            .nav-button button:hover {
                background-color: #c82333;
            }

        }

        @media (max-width: 375px) {

            .nav-button:first-child {
                margin-left: 0;
            }

            .nav-button:last-child {
                margin-right: 0;
            }

        }

    </style>

</head>

<body>

<div class="sidebar">
    <div class="container">

        <button type="button" id="sidebarToggle" class="btn btn-danger">
            <i class="fas fa-bars"></i>
        </button>

        @auth
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-circle fa-2x text-light"></i>
                </div>
                <div class="user-details text-white">
                    <h5>{{ explode(' ', auth()->user()->name)[0] }}</h5>
                    @if(auth()->user()->hasRole('admin'))
                        <p>Admin</p>
                    @elseif(auth()->user()->hasRole('worker'))
                        <p>Worker</p>
                    @elseif(auth()->user()->hasRole('viewer'))
                        <p>Viewer</p>
                    @endif
                </div>
            </div>

            <div class="navigation">

                <a href="{{route('index')}}" class="nav-button">
                    <i class="fas text-white fa-home sidebaricons"></i>
                    <span class="text-white">Home</span>
                </a>

                <a href="{{route('profile.show')}}" class="nav-button">
                    <i class="fas text-white fa-user sidebaricons" style="padding-left: 2px"></i>
                    <span class="text-white">Profile</span>
                </a>

            </div>

            <div class="dashboard-button">

                @if(auth()->user()->hasRole('admin'))

                    <a href="{{route('admin.dashboard')}}" class="nav-button">
                        <i class="fas text-white fa-poll-h sidebaricons" style="padding-left: 2px"></i>

                        <span class="text-white">Dashboard</span>
                    </a>

                @endif

            </div>

            <div class="logout">
                <form action="/logout" method="post">
                    @csrf
                    <button type="submit">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>

        @endauth

    </div>

</div>

<div class="bottom-bar">
    <div class="nav-buttons-container">
        <a href="{{ route('index') }}" class="nav-button">
            <i class="fas text-white fa-home sidebaricons"></i>
            <span class="text-white">Home</span>
        </a>

        <a href="{{ route('profile.show') }}" class="nav-button">
            <i class="fas text-white fa-user sidebaricons"></i>
            <span class="text-white">Profile</span>
        </a>

        @auth()

            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}" class="nav-button">
                    <i class="fas text-white fa-poll-h sidebaricons"></i>
                    <span class="text-white">Dashboard</span>
                </a>
            @endif

            <div class="nav-button">
                <form action="/logout" method="post">
                    @csrf
                    <button type="submit">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>

        @endauth

    </div>

</div>

<div class="top-bar">
    <div class="company-info">
        <div class="company-logo">
            <!-- Image of the company -->
            <a href="{{route('index')}}"><img src="{{ asset('img/logo.png') }}" alt="Company Logo"></a>
        </div>
        <a class="company-name" href="{{route('index')}}" style="color: white; text-decoration: none;">Motherson</a>

    </div>
    <div class="user-info">
        <div class="user-details text-white">
            <h5>
                <a href="{{route('profile.show')}}" style="color: white; text-decoration: none;">

                    @auth()
                        @if(auth()->user()->hasRole('admin'))
                            Admin,
                        @elseif(auth()->user()->hasRole('worker'))
                            Worker,
                        @elseif(auth()->user()->hasRole('viewer'))
                            Viewer,
                        @endif
                        {{ explode(' ', auth()->user()->name)[0] }}
                    @endauth

                </a>
            </h5>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    $(document).ready(function(){
        $('#sidebarToggle').on('click', function(){
            $('.sidebar').toggleClass('open');
            $('.main-content').toggleClass('open');
        });
    });

</script>

</body>
</html>
