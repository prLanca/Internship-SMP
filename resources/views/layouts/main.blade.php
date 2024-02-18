<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Motherson</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script src="https://appsforoffice.microsoft.com/lib/1/hosted/office.js"></script>


    <style>
        /* Estilos para o conteúdo principal */
        .main-content {
            margin-left: 60px; /* Largura da barra lateral fechada */
            padding: 20px; /* Espaçamento interno */
            transition: margin-left 0.5s ease-out 0.1s; /* Adiciona uma transição suave ao abrir e fechar */
            width: calc(100% - 60px); /* Largura do conteúdo principal */
        }

        /* Media query for smaller screens */
        @media (max-width: 768px) {
            main {
                margin-left: 0; /* Reset margin for smaller screens */
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
