<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .error-page {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .error-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .error-page h1 {
            font-size: 14vw;
            color: #333;
            margin: 0;
        }

        .error-page p {
            font-size: 2vw;
            color: #666;
            margin-top: 0;
            margin-bottom: 0;
        }

        .error-page .buttons-con {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .error-page .buttons-con .action-link-wrap a {
            display: inline-block;
            background-color: #b40000;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin: 0 10px;
            transition: background-color 0.3s;
        }

        .error-page .buttons-con .action-link-wrap a:hover {
            background-color: #730000;
        }

        /* Animation for the image */
        @keyframes bounce {
            0%, 100% { transform: translateY(4vh); }
            50% { transform: translateY(-2vh); }
        }

        .error-page img {
            animation: bounce 2s infinite;
            width: 450px;
            max-width: 100%;
            border-radius: 15px;
        }

        /* Media query for medium-sized screens */
        @media screen and (max-width: 992px) {
            .error-page {
                flex-direction: column;
            }

            .error-page img {
                margin-right: 0;
                margin-bottom: 20px;
            }

            .error-page .error-content {
                margin-left: 0;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="container error-page">
    <!-- Your logo SVG code goes here -->
    <div class="p-2 image">
        <img src="{{ asset('img/logo.png') }}" alt="Motherson Logo" class="img-fluid rounded-pill   ">
    </div>

    <div class="error-content">
        <h1>404</h1>
        <p>Page not found</p>
        <div class="buttons-con">
            <div class="action-link-wrap">
                <a onclick="history.back(-1)" class="btn btn-danger link-back-button">Go Back</a>
                <a href="/" class="btn btn-primary">Go to Home Page</a>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS (Optional) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
