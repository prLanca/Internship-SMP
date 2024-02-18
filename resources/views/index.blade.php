@extends('layouts.main')
@section('content')

    <!DOCTYPE html>
<html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>

<head>

    <style>

        .row {
            display: flex;
            flex-wrap: wrap; /* Allow screens to wrap to the next line */
            justify-content: center; /* Center screens horizontally */
            margin: -5px; /* Add negative margin to compensate for margin on screens */
        }

        .screen {
            width: calc(50% - 20px); /* Set width for screens to occupy half of the container width */
            height: 46vh; /* Adjust height as needed */
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            border: 2px solid #ff0000; /* Green border */
            border-radius: 10px; /* Rounded corners */
            margin: 5px; /* Add margin between screens */
            cursor: pointer; /* Add cursor pointer to indicate clickability */
            transition: border-color 0.3s; /* Smooth transition for border color */
        }

        @media (max-width: 768px) {
            /* Adjust styles for small screens (mobile) */
            .screen {
                width: calc(100% - 20px); /* Set width to occupy full width on small screens */
                height: 20vh; /* Adjust height as needed */
            }
        }

        .screen:hover {
            border-color: #730000; /* Darker green border on hover */
        }

        .screen-content {
            font-size: 24px; /* Adjust font size as needed */
        }

        /* Additional styling for touch screens */
        .touch-screen {
            background-color: #e0e0e0;
        }

        /* Hide content initially */
        .content {
            display: none;
        }

        /* Estilizando o input do tipo file */
        .custom-file {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .custom-file-input {
            position: relative;
            z-index: 2;
            width: 100%;
            height: calc(1.5em + .75rem + 2px);
            margin: 0;
            opacity: 0;
        }

        .custom-file-label {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1;
            height: calc(1.5em + .75rem);
            padding: .375rem .75rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            overflow: hidden;
        }

    </style>
</head>

<body>

<button id="backButton" onclick="goBack()" class="btn btn-danger" style="margin-bottom: 20px; display: none;">
    <i class="fas fa-arrow-left"></i> Go Back
</button>

<div class="row">

    <div class="screen touch-screen" onclick="showContent('montagem')">

        <div class="screen-content">
            Montagem
        </div>

    </div>

    <div class="screen touch-screen" onclick="showContent('qualidade')">
        <div class="screen-content">
            Qualidade
        </div>
    </div>

    <div class="screen touch-screen" onclick="showContent('content3')">
        <div class="screen-content">
            Exemplo 3
        </div>
    </div>

    <div class="screen touch-screen" onclick="showContent('content4')">
        <div class="screen-content">
            Exemplo 4
        </div>
    </div>

</div>

<!-- Content from screen 1 -->
<div id="montagem" class="content">
    <!-- File Upload Form -->
    <form id="uploadForm" method="POST" action="{{ route('admin.upload.montagem') }}" enctype="multipart/form-data" class="my-4">
        @csrf
        <div class="form-group">
            <label for="file" class="form-label">Insert File:</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" name="file" onchange="displayFileName()">
                <label class="custom-file-label" for="file" id="fileLabel">Choose file</label>
            </div>
        </div>
        <button type="submit" class="btn btn-danger">Upload</button>
    </form>

    <!-- File List Table -->
    <h3 class="mt-4">Uploaded Files</h3>
    <div class="table-responsive">
        <table class="table mt-2">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>File Name</th>
                <th>Uploaded At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @php
                $montagemFiles = Storage::disk('public')->files('Montagem');
            @endphp
            @foreach($montagemFiles as $index => $file)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ basename($file) }}</td>
                    <td>{{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</td>
                    <td>
                        <button type="button" class="btn btn-success" onclick="openPreview('{{ Storage::url($file) }}')">Preview</button>
                        <button type="button" class="btn btn-danger" onclick="deleteFile('{{ $file }}')">Delete</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Content from screen 2 -->
<div id="qualidade" class="content">

    <form id="uploadForm" method="POST" action="{{ route('admin.upload.qualidade') }}" enctype="multipart/form-data" class="my-4">
        @csrf
        <div class="form-group">
            <label for="file" class="form-label">Insert File:</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" name="file" onchange="displayFileName()">
                <label class="custom-file-label" for="file" id="fileLabel">Choose file</label>
            </div>
        </div>
        <button type="submit" class="btn btn-danger">Upload</button>
    </form>

</div>

<!-- Content from screen 3 -->
<div id="content3" class="content">
    Content for Exemplo 3
</div>

<!-- Content from screen 4 -->
<div id="content4" class="content">
    Content for Exemplo 4
</div>

<!-- Modal for file preview -->
<div id="previewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePreview()">&times;</span>
        <iframe id="previewFrame" frameborder="0" width="500vh" height="500vh"></iframe>
        <div id="excelPreview"></div> <!-- Place to display Excel content -->
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

<script>

    // Function to show content and display back button
    function showContent(contentId) {
        // Hide all screens
        var screens = document.querySelectorAll('.screen');
        screens.forEach(function(screen) {
            screen.style.display = 'none';
        });

        // Show selected content
        var content = document.getElementById(contentId);
        if (content) {
            content.style.display = 'block';
            // Update URL with content ID
            window.location.hash = '#' + contentId;
            // Display back button
            document.getElementById('backButton').style.display = 'block';
        }
    }

    // Function to delete the files
    function deleteFile(filePath) {
        if (confirm("Tem certeza de que deseja excluir este arquivo?")) {
            $.ajax({
                url: "{{ route('admin.delete.file') }}", // Corrigido para usar a rota correta
                method: 'POST',
                data: { filePath: filePath },
                success: function(response) {

                    // Recarregue a página após a exclusão bem-sucedida

                    window.location.reload();
                },

                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Falha ao excluir arquivo.');
                }

            });

        }

    }

    /* Show the file name in the text field */
    function displayFileName() {
        var input = document.getElementById('file');
        var fileName = input.files[0].name;
        var label = document.getElementById('fileLabel');
        label.innerHTML = fileName;
    }

    // Function to parse the URL hash and show the corresponding content
    function showContentFromUrl() {
        var hash = window.location.hash;
        if (hash) {
            var contentId = hash.substring(1);
            showContent(contentId);
        }
    }

    // Call the function to show content based on URL hash when the page loads
    window.onload = function() {
        showContentFromUrl();
    };

    // Function to go back to the main screen and hide back button
    function goBack() {

        // Hide back button
        document.getElementById('backButton').style.display = 'none';

        window.location.hash = '';

        // Hide all content
        var content = document.querySelectorAll('.content');
        content.forEach(function(item) {
            item.style.display = 'none';
        });

        // Show all screens
        var screens = document.querySelectorAll('.screen');
        screens.forEach(function(screen) {
            screen.style.display = 'flex';
        });

        // Update URL to remove the content ID
        history.pushState(null, null, window.location.pathname);

    }

    // Prevent form submission on page refresh
    $(document).ready(function() {
        $('#uploadForm').submit(function(e) {
            e.preventDefault(); // Prevent default form submission
            // Submit form data using AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {

                    // Reload the page
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    alert('File upload failed');
                }
            });
        });
    });

    // Function to Preview the pdf file
    function openPreview(url) {
        if (url.toLowerCase().endsWith('.pdf')) {
            // Set the src attribute of the iframe
            document.getElementById('previewFrame').src = url;

            // Adjust the height of the iframe dynamically based on the modal's height
            var modalHeight = document.getElementById('previewModal').offsetHeight;
            document.getElementById('previewFrame').style.height = (modalHeight - 50) + 'px'; // Adjust 50px as needed

            // Display the modal
            document.getElementById('previewModal').style.display = 'block';
        } else {
            alert('Preview is not available for this file type.');
        }
    }

    // Function to display PowerPoint presentations
    function closePreview() {
        document.getElementById('previewModal').style.display = 'none';
        document.getElementById('previewFrame').src = '';
    }

</script>

</body>

</html>

@endsection
