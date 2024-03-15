<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Motherson Portal</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script src="https://appsforoffice.microsoft.com/lib/1/hosted/office.js"></script>

    <style>

        /* Styles for main content */
        .main-content {
            margin-left: 60px; /* width of the closed bar */
            padding: 20px; /* Intern padding */
            transition: margin-left 0.5s ease-out 0.1s; /* Smooth transition */
            width: calc(100% - 60px); /* width of the main content */
        }

        /* Media query for smaller screens */
        @media (max-width: 768px) {

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                margin-bottom: 6vh;
            }

        }

    </style>
</head>

<body>
<header class="header sticky-top">
    <div class="container-fluid p-0">
        @include('layouts.parts.sidebar')
    </div>
</header>

<main class="main-content">
    <h1 class="m-0">@yield('title')</h1>
    <div class="container-fluid p-0">
        @yield('content')
    </div>
</main>
</body>
</html>
