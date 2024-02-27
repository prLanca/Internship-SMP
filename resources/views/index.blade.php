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

        .file-drop-area {
            border: 1px solid #7e7e7e; /* Add a thin dashed border */
            border-radius: 15px; /* Add a border radius to the file drop area */
            padding: 20px; /* Add some padding to the file drop area */
            text-align: center; /* Center the text */
            font-size: 1.5vh; /* Adjust font size as needed */
            color: #333; /* Text color */
            transition: border-color 0.3s; /* Add a transition effect to the border */
        }

        .file-drop-area:hover {
            border: 1px solid #ff0000;
        }


        .file-item {
            margin-bottom: 5px; /* Add margin between file items */
            font-size: 16px; /* Adjust font size as needed */
            color: #333; /* Text color */
        }

        .upload-button {
            margin-top: 10px; /* Add margin above the button */
            padding: 10px 20px; /* Add padding to the button */
            color: #fff; /* Button text color */
            border: none; /* Remove button border */
            border-radius: 5px; /* Add border radius to the button */
            cursor: pointer; /* Add cursor pointer to the button */
            transition: background-color 0.3s; /* Add transition effect for hover */
        }

        /* #################### Media Screens #################### */

        @media (max-width: 768px) {
            /* Adjust styles for small screens (mobile) */
            .screen {
                width: calc(100% - 20px); /* Set width to occupy full width on small screens */
                height: 20vh; /* Adjust height as needed */
            }
        }


        /* Custom scrollbar styles */
        .scrollable-div {
            overflow-y: hidden; /* Hide the vertical scrollbar by default */
        }

        .scrollable-div:hover {
            overflow-y: auto; /* Display the vertical scrollbar on hover */
        }

        /* Track */
        .scrollable-div::-webkit-scrollbar {
            width: 10px; /* Set the width of the scrollbar */
        }

        /* Handle */
        .scrollable-div::-webkit-scrollbar-thumb {
            background: #888; /* Color of the scrollbar handle */
            border-radius: 5px; /* Rounded corners */
        }

        /* Handle on hover */
        .scrollable-div::-webkit-scrollbar-thumb:hover {
            background: #555; /* Darker color when hovered */
        }

    </style>

</head>

<body>

<!-- ########################################## Back Button ########################################## -->

<button id="backButton" onclick="goBack()" class="btn btn-danger" style="margin-bottom: 20px; display: none;">

    <i class="fas fa-arrow-left"></i> Go Back

</button>

<!-- ######################################## End Back Button ######################################## -->

<!-- ############################################ Screens ############################################ -->

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

<!-- ########################################## End Screens ########################################## -->

<!-- ######################################## Screens Content ######################################## -->

<!-- Content from screen 1 -->
<div id="montagem" class="content">

    <form id="montagemForm" action="{{ route('admin.upload.montagem') }}" method="POST" enctype="multipart/form-data">

        @csrf
        <label class="file-drop-area" id="fileDropArea">

            <div class="file-icon">

                <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

            </div>

            <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this)">

            <span class="file-label">Click to Upload a file</span>

            <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

        </label>


        <div class=" mt-4">
            <!-- Container for file cards -->
            <div id="droppedFilesContainer" class="row">

            </div>

            <hr id="simplehr" style="display: none;">

            <!-- Warning container -->
            <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
            </div>

            <!-- Mostra erro apenas da sreen que deu o erro de upload -->
            @if(isset($errorMessage) && $screen == 'Montagem')
                <div class="error-message">{{ $errorMessage }}</div>
            @endif

            <button id="uploadButton" class="upload-button bg-danger" onclick="return handleUpload('montagemForm', files)" style="display: none;">Upload</button>

        </div>

    </form>

    @php
        $montagemFiles = Storage::disk('public')->files('Montagem');
    @endphp

    <!-- File List Cards -->
    <h3 class="mt-4">Uploaded Files</h3>

    @if(!is_null($montagemFiles) && count($montagemFiles) > 0)

        <div class="container-fluid scrollable-div" style="max-height: 72vh; overflow-y: auto;">

            <div class="row mt-2">

                <div class="row mt-4">

                    @php
                        $montagemFiles = Storage::disk('public')->files('Montagem');
                        $rowCount = 0;
                        $maxrow = 6;
                    @endphp

                    @foreach($montagemFiles as $index => $file)
                        @if($rowCount % $maxrow == 0)
                        @endif

                        <div class="col-md-2 mb-4 d-flex">

                            <div class="card flex-fill position-relative" style="border-radius: 15px;">

                                <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0"> <!-- Adjust the height as needed -->

                                    <div class="card-title-container">

                                        <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                            {{ pathinfo($file, PATHINFO_FILENAME) }}
                                        </h5>

                                        <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

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

                                <div class="card-footer justify-content-center" style="border-radius: 0 0 15px 15px"> <!-- Add justify-content-center to align the buttons in the center -->

                                    @if($extension == 'pdf')
                                        <!-- Display preview button for PDF files -->
                                        <button type="button" class="btn btn-success btn-block preview-btn" onclick="openPreview('{{ Storage::url($file) }}')">Preview</button>
                                    @else
                                        <!-- Display download button for other file types -->
                                        <a href="{{ Storage::url($file) }}" class="btn btn-primary btn-block" download>Download</a>
                                    @endif


                                    <form action="{{ route('admin.delete.file') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="filePath" value="{{ $file }}">
                                        <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                    </form>

                                </div>

                            </div>

                        </div>


                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        </div>

    @else
        <div class="alert alert-danger mt-4" role="alert">
            No files uploaded yet.
        </div>
    @endif

</div>

<!-- Content from screen 2 -->
<div id="qualidade" class="content">

    <form action="{{ route('admin.upload.qualidade') }}" method="POST" enctype="multipart/form-data">

        @csrf
        <div>
            <label for="file">Choose File:</label>
            <input type="file" id="file" name="file">
        </div>

        @if(isset($errorMessage ) && $screen == 'Qualidade')
            <div class="error-message">{{ $errorMessage }}</div>
        @endif

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

<!-- ###################################### End Screens Content ###################################### -->

<!-- ###################################### Preview Screens PDF ###################################### -->

<!-- Modal for file preview -->
<div id="previewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePreview()">&times;</span>
        <iframe id="previewFrame" frameborder="0" width="1920vh" height="1080vh"></iframe>
        <div id="excelPreview"></div> <!-- Place to display Excel content -->
    </div>
</div>

<!-- #################################### End Preview Screens PDF #################################### -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script src="{{ asset('path/to/reveal.js/dist/reveal.js') }}"></script>

<script>

    let files = [];

    <!-- ############################### Scripts for Screens ############################### -->

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

            // Store the current screen state in local storage
            localStorage.setItem('currentScreen', contentId);
        }
    }

    // Function to parse the URL hash and show the corresponding content
    function showContentFromUrl() {
        var hash = window.location.hash;
        if (hash) {
            var contentId = hash.substring(1);
            showContent(contentId);
        }
    }

    // Function to set the initial screen state based on the stored value
    function setInitialScreenState() {
        var currentScreen = localStorage.getItem('currentScreen');
        if (currentScreen) {
            showContent(currentScreen);
        }
    }

    // Call the function to show content based on URL hash when the page loadsF
    window.onload = function() {
        showContentFromUrl();
        setInitialScreenState();
    };

    <!-- ############################# End Scripts for Screens ############################# -->

    <!-- ################################# Back button ################################# -->

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

        // Clear the stored screen state
        localStorage.removeItem('currentScreen');
    }

    <!-- ############################### End Back button ############################### -->

    <!-- ############################### File PDF Preview ############################### -->

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

    <!-- ############################# End File PDF Preview ############################# -->

    <!-- ############################## File Display before uplaoding ############################# -->

    // Function to display selected files when using file input
    function displaySelectedFiles(input) {
        const newFiles = input.files;

        // Append each new file to the existing files array
        Array.from(newFiles).forEach(newFile => {
            files.push(newFile);
        });

        // Display dropped files
        displayDroppedFiles(files);
    }

    function displayDroppedFiles(files) {
        const droppedFilesContainer = document.getElementById('droppedFilesContainer');

        // Clear previous files from the container
        droppedFilesContainer.innerHTML = '';

        // Display file cards with improved design and margin between them
        Array.from(files).forEach((file, index) => {
            // Create card container
            const card = document.createElement('div');
            card.classList.add('card', 'mb-3', 'rounded', 'shadow', 'ml-1', 'mr-1'); // Add Bootstrap classes for card, margin, rounded corners, and shadow

            // Check if the file type is supported
            const isSupportedFileType = ['pdf', 'xlsx', 'xlsm', 'ppt', 'pptx', 'xls', 'docx'].includes(getFileExtension(file.name));
            console.log(`File: ${file.name}, Supported: ${isSupportedFileType}`); // Log if the file is supported or not

            // Conditional styling based on file type
            if (!isSupportedFileType) {
                card.classList.add('border', 'border-danger', 'border-3'); // Add red border for unsupported file types and make it thicker

                // Append the warning message to a container outside the card
                const warningContainer = document.getElementById('warning-container'); // Replace 'warning-container' with the ID of your desired container
                if (warningContainer) {
                    warningContainer.style.display = 'block'; // Show the warning container
                } else {
                    console.error("Warning container not found."); // Log an error if the container is not found
                }
            }

            // Card content
            card.innerHTML =
                `
                <div class="d-flex justify-content-end"> <!-- Align delete button to the right -->
                    <button type="button" style="color: grey; text-decoration: none;" class="btn btn-link delete-btn position-absolute mt-0 delbuttoncard" onclick="deleteDisplayedFile(${index})">&times;</button>
                </div>
                <div class="card-body d-flex flex-column ${isSupportedFileType ? 'bg-light' : 'bg-gradient-warning'}">
                    <div class="upload-preview-wrapper d-flex justify-content-center align-items-center mb-2" style="height: 6vh; width: 18vh; overflow: hidden;"> <!-- Fixed height wrapper -->
                        <!-- Add the icon here -->
                        <p class="card-text mb-0">${getFileFormatIcon(getFileExtension(file.name))}</p>
                    </div>
                    <div class="card-body m-0 p-1 text-center" style="max-width: 18vh">
                        <h6 class="card-title mb-1" style="font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${file.name}</h6>
                        <p class="card-text mb-0">${formatFileSize(file.size)}</p>
                    </div>
                </div>
            `;

            // Append card to the container
            droppedFilesContainer.appendChild(card);
        });

        // Show upload button if there are files
        const uploadButton = document.getElementById('uploadButton');
        const simplehr = document.getElementById('simplehr');
        uploadButton.style.display = files.length > 0 ? 'block' : 'none';
        simplehr.style.display = files.length > 0 ? 'block' : 'none';
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
                return '<img src="{{ asset("img/format_icons/excel.png") }}" alt="XLSX" style="width: 30px; height: 30px;">'; // XLSX and XLSM file icon
            case 'pptx':
                return '<img src="{{ asset("img/format_icons/powerpoint.png") }}" alt="PPTX" style="width: 30px; height: 30px;">'; // PPTX file icon
            default:
                return '<img src="{{ asset("img/format_icons/errorfile.png") }}" alt="File" style="width: 40px; height: 40px;">'; // Default file icon
        }
    }

    <!-- ############################## End File Display before uplaoding ############################# -->

    <!-- ################################## File Upload ################################## -->

    // Function to handle the upload
    function handleUpload(formId) {
        if (files.length > 0) {
            uploadFiles(formId, files); // Call uploadFiles with the form ID and selected files
        } else {
            console.error('No files have been selected.');
        }
    }

    // Function to upload files
    function uploadFiles(formId, files) {
        console.log('Uploading files:', files); // Log files for debugging

        const form = document.getElementById(formId); // Get the form dynamically using formId
        const formData = new FormData(form);

        // Append each file to the FormData object
        Array.from(files).forEach(file => {
            formData.append('files[]', file);
        });

        console.log('FormData:', formData); // Log FormData object for debugging

        // Submit the form with FormData
        $.ajax({
            url: form.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Calculate the delay based on the number and size of files
                const totalFileSize = Array.from(files).reduce((total, file) => total + file.size, 0);
                const uploadDelay = Math.max(5000, totalFileSize / 1000); // Minimum delay of 5 seconds or 1 second per KB

                // Delay before refreshing the page
                window.location.reload();

            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('File upload failed.');

                // Handle individual file errors
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    files.forEach(file => {
                        if (errors[file.name]) {
                            console.error(`Error uploading file ${file.name}: ${errors[file.name]}`);
                            // You can handle individual file errors here, such as displaying an alert or updating UI
                        } else {
                            console.log(`File ${file.name} uploaded successfully.`);
                            // You can handle successful uploads here, such as updating UI
                        }
                    });
                }
            }
        });
    }

    <!-- ################################ End File Upload ################################ -->

</script>

</body>

</html>

@endsection
