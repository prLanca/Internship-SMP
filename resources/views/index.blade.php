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

        .error-container {
            display: none; /* Initially hide the error container */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .error-message {
            text-align: center;
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .error-message h2 {
            color: #ff0000;
        }

        .error-message p {
            margin-bottom: 10px;
        }

        /* Custom CSS */
        .preview-btn,
        .delete-btn {
            height: 38px; /* Adjust the height as needed */
            line-height: 1.5; /* Adjust the line-height to vertically center the text */
        }

        .file-drop-area {
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            display: block; /* Use block display to ensure the entire label is clickable */
        }

        .file-icon {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .file-label {
            font-size: 16px;
            display: block; /* Ensure the label text is displayed properly */
        }

        .file-input {
            display: none;
        }

        /* CSS for file drop area when dragging and dropping */
        .file-drop-area.dragged-over {
            border-color: #007bff; /* Change border color when dragged over */
            background-color: rgba(0, 123, 255, 0.1); /* Add a light blue background color when dragged over */
        }

        /* Additional styling for the file icon when dragged over */
        .file-drop-area.dragged-over .file-icon {
            color: #007bff; /* Change color of the file icon when dragged over */
        }




        .file-item {
            margin-bottom: 5px; /* Add margin between file items */
            font-size: 16px; /* Adjust font size as needed */
            color: #333; /* Text color */
        }

        .upload-button {
            margin-top: 10px; /* Add margin above the button */
            padding: 10px 20px; /* Add padding to the button */
            background-color: #007bff; /* Button background color */
            color: #fff; /* Button text color */
            border: none; /* Remove button border */
            border-radius: 5px; /* Add border radius to the button */
            cursor: pointer; /* Add cursor pointer to the button */
            transition: background-color 0.3s; /* Add transition effect for hover */
        }

        .upload-button:hover {
            background-color: #0056b3; /* Darken button background color on hover */
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

    <form id="montagemForm" action="{{ route('admin.upload.montagem') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="file-drop-area" id="fileDropArea">
            <div class="file-icon">
                <i class="fas fa-file-upload"></i>
            </div>
            <input type="file" class="file-input" name="files[]" multiple onchange="uploadFiles(this)">
            <span class="file-label">Click or Drag & Drop to Upload</span>
        </label>
    </form>

    <div class=" mt-4">
        <!-- Container for file cards -->
        <div id="droppedFilesContainer" class="row">

        </div>

        <button id="uploadButton" class="upload-button" onclick="return uploadFiles(this)" style="display: none;">Upload</button>

    </div>



    <!-- File List Cards -->
    <h3 class="mt-4">Uploaded Files</h3>
    <div class="row mt-2">
        @php
            $montagemFiles = Storage::disk('public')->files('Montagem');
            $rowCount = 0;
        @endphp
        @foreach($montagemFiles as $index => $file)
            @if($rowCount % 6 == 0)
    </div>

    <div class="row mt-4">

        @endif
        <div class="col-md-2 mb-4 d-flex">
            <div class="card flex-fill position-relative" style="border-radius: 15px;">
                <div class="card-header" style="height: 8vh;"> <!-- Adjust the height as needed -->
                    <div class="card-title-container">
                        <h5 class="card-title" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                            {{ pathinfo($file, PATHINFO_FILENAME) }}
                        </h5>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <p class="card-text" style="margin-bottom: 0;">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>
                    @php
                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                    @endphp
                    <p class="mt-4 mb-0">File Format:
                        @if($extension == 'pdf')
                            <img src="{{asset('img/format_icons/pdf.png')}}" alt="pdf" style="max-height: 25px;">
                        @elseif($extension == 'doc' || $extension == 'docx')
                            <img src="{{asset('img/format_icons/word.png')}}" alt="word" style="max-height: 25px;">
                        @elseif($extension == 'xls' || $extension == 'xlsx')
                            <img src="{{asset('img/format_icons/excel.png')}}" alt="excel" style="max-height: 25px;">
                        @else
                            <img src="{{asset('img/format_icons/powerpoint.png')}}" alt="powerpoint" style="max-height: 25px;">
                        @endif
                    </p>
                </div>
                <div class="card-footer justify-content-center"> <!-- Add justify-content-center to align the buttons in the center -->
                    <button type="button" class="btn btn-success btn-block preview-btn" onclick="openPreview('{{ Storage::url($file) }}')">Preview</button>
                    <button type="button" class="btn btn-danger btn-block delete-btn" onclick="deleteFile('{{ $file }}')">Delete</button>
                </div>
            </div>
        </div>


        @php
            $rowCount++;
        @endphp
        @endforeach

    </div>

</div>

<!-- Content from screen 2 -->
<div id="qualidade" class="content">

    <form action="{{ route('admin.upload.qualidade') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="file">Choose File:</label>
            <input type="file" id="file" name="file">
        </div>
        <button type="submit">Upload File</button>
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
                $montagemFiles = Storage::disk('public')->files('Qualidade');
            @endphp

            @foreach($montagemFiles as $index => $file)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ basename($file) }}</td>
                    <td>{{ date('Y-m-d H:i', Storage::disk('public')->lastModified($file)) }}</td>
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

<!-- Content from screen 3 -->
<div id="content3" class="content">
    Content for Exemplo 3
</div>

<!-- Content from screen 4 -->
<div id="content4" class="content">
    Content for Exemplo 4
</div>

@if(isset($errorMessage))
    <div class="error-message">{{ $errorMessage }}</div>
@endif

<!-- Modal for file preview -->
<div id="previewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePreview()">&times;</span>
        <iframe id="previewFrame" frameborder="0" width="1920vh" height="1080vh"></iframe>
        <div id="excelPreview"></div> <!-- Place to display Excel content -->
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script src="{{ asset('path/to/reveal.js/dist/reveal.js') }}"></script>

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
    function displayFileName(input) {
        const fileName = input.files[0].name;
        document.getElementById('file-label').innerText = fileName;
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

    document.addEventListener('DOMContentLoaded', function() {

        // Check if there's an error message in the URL query parameter
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');

        if (error) {
            // Display the error container and set the error description
            document.getElementById('error-container').style.display = 'block';
            document.getElementById('error-description').textContent = error;
        }
    });

    /* FUNCTIONS TO DRAG AND DROP FILE --------------------------------------------------------------------------------- */

    // Function to handle drag over event
    function handleDragOver(event) {
        event.preventDefault();
        event.stopPropagation();
        event.target.classList.add('dragged-over'); // Add 'dragged-over' class to file drop area
    }

    // Function to handle drag enter event
    function handleDragEnter(event) {
        event.preventDefault();
        event.stopPropagation();
        event.target.classList.add('dragged-over'); // Add 'dragged-over' class to file drop area
    }

    // Function to handle drag leave event
    function handleDragLeave(event) {
        event.preventDefault();
        event.stopPropagation();
        event.target.classList.remove('dragged-over'); // Remove 'dragged-over' class from file drop area
    }

    // Function to handle drop event
    function handleDrop(event) {
        event.preventDefault();
        event.stopPropagation();
        event.target.classList.remove('dragged-over'); // Remove 'dragged-over' class from file drop area

        // Get dropped files
        const files = event.dataTransfer.files;

        // Display dropped files below the box
        displayDroppedFiles(files);
    }

    function displayDroppedFiles(files) {
        const droppedFilesContainer = document.getElementById('droppedFilesContainer');

        // Clear previous files
        droppedFilesContainer.innerHTML = '';

        // Display file cards with improved design and margin between them
        Array.from(files).forEach((file, index) => {
            // Create card container
            const card = document.createElement('div');
            card.classList.add('card', 'mb-3', 'rounded', 'shadow'); // Add Bootstrap classes for card, margin, rounded corners, and shadow

            // Card content
            card.innerHTML =
                `
            <div class="card-body d-flex flex-column bg-light">
                <div class="upload-preview-wrapper d-flex justify-content-center align-items-center mb-2" style="height: 6vh; width: 18vh; overflow: hidden;"> <!-- Fixed height wrapper -->
                    <!-- Add the icon here -->
                    <p class="card-text mb-0" >${getFileFormatIcon(getFileExtension(file.name))}</p>
                </div>
                <div class="card-body m-0 p-1 text-center">
                    <h6 class="card-title mb-1" style="font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${file.name}</h6>
                    <p class="card-text mb-0">${formatFileSize(file.size)}</p>
                </div>
            </div>
        `;

            // Add margin between cards if there are multiple files
            if (index > 0) {
                card.style.marginLeft = '10px'; // Adjust the margin as needed
            }

            // Append card to the container
            droppedFilesContainer.appendChild(card);
        });

        // Show upload button if there are files
        const uploadButton = document.getElementById('uploadButton');
        if (files.length > 0) {
            uploadButton.style.display = 'block';
        } else {
            uploadButton.style.display = 'none';
        }
    }

    // Function to get file extension from file name
    function getFileExtension(fileName) {
        return fileName.split('.').pop().toLowerCase();
    }

    // Function to format file size
    function formatFileSize(size) {
        if (size < 1024) {
            return size + ' bytes';
        } else if (size >= 1024 && size < 1048576) {
            return (size / 1024).toFixed(2) + ' KB';
        } else if (size >= 1048576) {
            return (size / 1048576).toFixed(2) + ' MB';
        }
    }

    // Function to get file format icon based on file extension
    function getFileFormatIcon(fileExtension) {
        switch (fileExtension.toLowerCase()) {
            case 'pdf':
                return '<img src="{{ asset("img/format_icons/pdf.png") }}" alt="PDF" style="width: 30px; height: 30px;">'; // PDF file icon
            case 'docx':
                return '<img src="{{ asset("img/format_icons/word.png") }}" alt="DOCX" style="width: 30px; height: 30px;">'; // DOCX file icon
            case 'xlsx':
            case 'xlsm':
                return '<img src="{{ asset("img/format_icons/xlsx.png") }}" alt="XLSX" style="width: 30px; height: 30px;">'; // XLSX and XLSM file icon
            case 'pptx':
                return '<img src="{{ asset("img/format_icons/pptx.png") }}" alt="PPTX" style="width: 30px; height: 30px;">'; // PPTX file icon
            default:
                return '<img src="{{ asset("img/format_icons/default.png") }}" alt="File" style="width: 30px; height: 30px;">'; // Default file icon
        }
    }

    // Add event listeners for drag events
    const fileDropArea = document.getElementById('fileDropArea');
    fileDropArea.addEventListener('dragover', handleDragOver);
    fileDropArea.addEventListener('dragenter', handleDragEnter);
    fileDropArea.addEventListener('dragleave', handleDragLeave);
    fileDropArea.addEventListener('drop', handleDrop);

    // Function to upload files
    function uploadFiles(files) {
        const form = document.getElementById('montagemForm'); // Assuming the form id is 'montagemForm'
        const formData = new FormData(form);

        // Get files from the droppedFilesContainer
        const droppedFilesContainer = document.getElementById('droppedFilesContainer');
        const fileCards = droppedFilesContainer.querySelectorAll('.card');

        // Append each file to the FormData object
        fileCards.forEach(card => {
            const fileName = card.querySelector('.card-title').innerText;
            formData.append('files[]', fileName);
        });

        // Submit the form with FormData
        $.ajax({
            url: form.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Reload the page or update UI as needed
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('File upload failed.');
            }
        });
    }

    // Function to upload files from droppedFilesContainer
    function uploadDroppedFiles() {
        const form = document.getElementById('montagemForm'); // Assuming the form id is 'montagemForm'
        const formData = new FormData(form);

        // Get files from the droppedFilesContainer
        const droppedFilesContainer = document.getElementById('droppedFilesContainer');
        const fileCards = droppedFilesContainer.querySelectorAll('.card');

        // Append each file to the FormData object
        fileCards.forEach(card => {
            const fileName = card.querySelector('.card-title').innerText;
            formData.append('files[]', fileName);
        });

        // Submit the form with FormData
        $.ajax({
            url: form.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Reload the page or update UI as needed
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('File upload failed.');
            }
        });
    }

    // Function to upload files when clicking the upload button
    function uploadFilesOnClick() {
        const form = document.getElementById('montagemForm'); // Assuming the form id is 'montagemForm'
        const formData = new FormData(form);

        const fileInput = document.getElementById('fileInput');
        if (fileInput && fileInput.files.length > 0) {
            Array.from(fileInput.files).forEach(file => {
                formData.append('files[]', file);
            });
        }

        // Submit the form with FormData
        $.ajax({
            url: form.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Reload the page or update UI as needed
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('File upload failed.');
            }
        });
    }


    /* ----------------------------------------------------------------------------------------------------------------- */

</script>

</body>

</html>

@endsection
