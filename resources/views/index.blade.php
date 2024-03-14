@extends('layouts.main')
@section('content')

    @if((auth()->user()->hasRole('worker') || auth()->user()->hasRole('viewer')) && !auth()->user()->hasVerifiedEmail())

    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Email not verified!</h4>
        <p>Please verify your email address to access all features of the application.</p>
        <hr>

        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn btn-danger">Resend Verification Email</button>
        </form>

    </div>

@else

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
                padding-top: 25px;
            }

            .screen {
                width: calc(25% - 20px);
                height: 30vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: #ffffff;
                border: 2px solid #ff0000;
                border-radius: 10px;
                margin: 5px;
                cursor: pointer;
                transition: border-color 0.3s, transform 0.3s, background-color 0.2s; /* Add transition for border color, background color, and transform */
            }

            .screen:hover {
                border-color: #ff3c3c;
                background-color: #ffd4d4;
                transform: scale(1.02); /* Scale up by 2% on hover */
            }

            .screen-content {
                font-size: 24px; /* Adjust font size as needed */
                text-align: center;
            }


            /* Hide content initially */
            .content {
                display: none;
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

                .screen:last-child {
                    margin-bottom: 8vh; /* Remove margin bottom for the last screen */
                }

                .row {

                }
            }

            /* Custom scrollbar styles */
            .scrollable-div {
                overflow-y: hidden; /* Hide the vertical scrollbar by default */
                position: relative; /* Position relative for absolute positioning of fade */
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
                display: none; /* Hide the scrollbar handle by default */
            }

            /* Handle on hover */
            .scrollable-div:hover::-webkit-scrollbar-thumb {
                display: block; /* Display the scrollbar handle on hover */
                background: #555; /* Darker color when hovered */
            }

            .hidden {
                display: none;
            }


            .uploader-checkboxes-box {
                border: 1px solid #ced4da; /* Border color */
                border-radius: 10px; /* Rounded corners */
                padding: 20px; /* Padding */
                background-color: #f8f9fa; /* Background color */

                max-height: 16vh; /* Maximum height */
                overflow-y: auto; /* Add scrollbar when needed */
            }

            .uploader-checkboxes-title {
                margin-top: 0;
                margin-bottom: 10px;
                color: #495057; /* Text color */
            }

            .uploader-checkboxes {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }

            .uploader-checkboxes .btn {
                cursor: pointer;
            }

            .uploader-checkboxes .btn input[type="checkbox"] {
                position: absolute;
                clip: rect(0, 0, 0, 0);
                pointer-events: none;
            }

            .uploader-checkboxes .btn input[type="checkbox"] + label {
                margin-bottom: 0;
            }

            .uploader-checkboxes .btn input[type="checkbox"]:checked + label {
                background-color: #007bff;
                color: #fff;
                border-color: #007bff;
            }

            .uploader-checkboxes .btn-group > .btn {
                position: relative;
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

        <div class="screen touch-screen" onclick="showContent('injecao')">
            <div class="screen-content">
                Injeção
            </div>
        </div>

        <div class="screen touch-screen" onclick="showContent('pintura')">
            <div class="screen-content">
                Pintura
            </div>
        </div>

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

        <div class="screen touch-screen" onclick="showContent('manutencao')">
            <div class="screen-content">
                Manutenção
            </div>
        </div>

        <div class="screen touch-screen" onclick="showContent('engenharia')">
            <div class="screen-content">
                Engenharia
            </div>
        </div>

        <div class="screen touch-screen" onclick="showContent('higiene')">
            <div class="screen-content">
                Higiene e Segurança
            </div>
        </div>

        <div class="screen touch-screen" onclick="showContent('lean')">
            <div class="screen-content">
                Lean
            </div>
        </div>

        <div class="screen touch-screen" onclick="showContent('qcdd')">
            <div class="screen-content">
                QCDD
            </div>
        </div>

        <div class="screen touch-screen" onclick="showContent('rh')">
            <div class="screen-content">
                Recursos Humanos
            </div>
        </div>

        <div class="screen touch-screen" onclick="showContent('empty')">
            <div class="screen-content">
                Empty
            </div>
        </div>

        <div class="screen touch-screen" onclick="showContent('empty2')">
            <div class="screen-content">
                Empty2
            </div>
        </div>

    </div>

    <!-- ########################################## End Screens ########################################## -->

    <!-- ######################################## Screens Content ######################################## -->

    <div id="injecao" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="injecaoForm" action="{{ route('admin.upload.injecao') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'injecao')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainerinjecao" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'Injeção')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtoninjecao" class="upload-button bg-danger" onclick="return handleUpload('injecaoForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $injecaoFiles = Storage::disk('public')->files('Injecao');
        @endphp

        <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($injecaoFiles) && count($injecaoFiles) > 0)

            <div class="container-fluid mt-4 mb-4">

                <div class="container-fluid mt-4 mb-4">

                    <div class="uploader-checkboxes-box mb-4">

                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>

                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="injecao">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($injecaoFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="injecao"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>



                <input type="text" id="file-search" class="form-control mb-2" data-container="injecao" placeholder="Search by filename">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="injecao">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">


                <div class="row mt-2 file-card-container" id="injecao">

                    @php
                        $injecaoFiles = Storage::disk('public')->files('Injecao');
                        $rowCount = 0;
                    @endphp

                    @foreach($injecaoFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">

                                        {{ substr(strrchr($file, "_"), 1) }}

                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="pintura" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="pinturaForm" action="{{ route('admin.upload.pintura') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'pintura')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainerpintura" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'Pintura')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtonpintura" class="upload-button bg-danger" onclick="return handleUpload('pinturaForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $pinturaFiles = Storage::disk('public')->files('Pintura');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($pinturaFiles) && count($pinturaFiles) > 0)


                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="pintura">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($pinturaFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="pintura"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>


            <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" data-container="pintura" placeholder="Search by filename">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="pintura">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">


                <div class="row mt-2 file-card-container" id="pintura">

                    @php
                        $pinturaFiles = Storage::disk('public')->files('Pintura');
                        $rowCount = 0;
                    @endphp

                    @foreach($pinturaFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>


                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="montagem" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="montagemForm" action="{{ route('admin.upload.montagem') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'montagem')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainermontagem" class="row">

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

                        <button id="uploadButtonmontagem" class="upload-button bg-danger" onclick="return handleUpload('montagemForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $montagemFiles = Storage::disk('public')->files('Montagem');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($montagemFiles) && count($montagemFiles) > 0)

                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="montagem">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($montagemFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="montagem"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

            <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" data-container="montagem" placeholder="Search by filename">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>

                    <select class="custom-select" id="sort-select" data-container="montagem">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>

                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">

                <div class="row mt-2 file-card-container" id="montagem">

                    @php
                        $montagemFiles = Storage::disk('public')->files('Montagem');
                        $rowCount = 0;
                    @endphp

                    @foreach($montagemFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="manutencao" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="manutencaoForm" action="{{ route('admin.upload.manutencao') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'manutencao')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainermanutencao" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'Manutenção')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtonmanutencao" class="upload-button bg-danger" onclick="return handleUpload('manutencaoForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $manutencaoFiles = Storage::disk('public')->files('Manutencao');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($manutencaoFiles) && count($manutencaoFiles) > 0)

                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="manutencao">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($manutencaoFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="manutencao"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

            <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" placeholder="Search by filename" data-container="manutencao">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="manutencao">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">


                <div class="row mt-2 file-card-container" id="manutencao">

                    @php
                        $manutencaoFiles = Storage::disk('public')->files('Manutencao');
                        $rowCount = 0;
                    @endphp

                    @foreach($manutencaoFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="qualidade" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="qualidadeForm" action="{{ route('admin.upload.qualidade') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'qualidade')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainerqualidade" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'Qualidade')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtonqualidade" class="upload-button bg-danger" onclick="return handleUpload('qualidadeForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $qualidadeFiles = Storage::disk('public')->files('Qualidade');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($qualidadeFiles) && count($qualidadeFiles) > 0)

                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="qualidade">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($qualidadeFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="qualidade"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

            <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" placeholder="Search by filename" data-container="qualidade">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="qualidade">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">


                <div class="row mt-2 file-card-container" id="qualidade">

                    @php
                        $qualidadeFiles = Storage::disk('public')->files('Qualidade');
                        $rowCount = 0;
                    @endphp

                    @foreach($qualidadeFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="engenharia" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="engenhariaForm" action="{{ route('admin.upload.engenharia') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'engenharia')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainerengenharia" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'Engenharia')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtonengenharia" class="upload-button bg-danger" onclick="return handleUpload('engenhariaForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $engenhariaFiles = Storage::disk('public')->files('Engenharia');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($engenhariaFiles) && count($engenhariaFiles) > 0)

                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="engenharia">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($engenhariaFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="engenharia"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" placeholder="Search by filename" data-container="engenharia">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="engenharia">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">

                <div class="row mt-2 file-card-container" id="engenharia">

                    @php
                        $engenhariaFiles = Storage::disk('public')->files('Engenharia');
                        $rowCount = 0;
                    @endphp

                    @foreach($engenhariaFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="higiene" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="higieneForm" action="{{ route('admin.upload.higiene') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'higiene')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainerhigiene" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'Higiene')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtonhigiene" class="upload-button bg-danger" onclick="return handleUpload('higieneForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $higieneFiles = Storage::disk('public')->files('Higiene');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($higieneFiles) && count($higieneFiles) > 0)

                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="higiene">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($higieneFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="higiene"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>


                <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" placeholder="Search by filename" data-container="higiene">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="higiene">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">


                <div class="row mt-2 file-card-container" id="higiene">

                    @php
                        $higieneFiles = Storage::disk('public')->files('Higiene');
                        $rowCount = 0;
                    @endphp

                    @foreach($higieneFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="lean" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="leanForm" action="{{ route('admin.upload.lean') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'lean')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainerlean" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'Lean')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtonlean" class="upload-button bg-danger" onclick="return handleUpload('leanForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $leanFiles = Storage::disk('public')->files('Lean');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($leanFiles) && count($leanFiles) > 0)

                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="lean">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($leanFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="lean"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>


                <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" placeholder="Search by filename" data-container="lean">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="lean">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">


                <div class="row mt-2 file-card-container" id="lean">

                    @php
                        $leanFiles = Storage::disk('public')->files('Lean');
                        $rowCount = 0;
                    @endphp

                    @foreach($leanFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="qcdd" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="qcddForm" action="{{ route('admin.upload.qcdd') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'qcdd')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainerqcdd" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'QCDD')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtonqcdd" class="upload-button bg-danger" onclick="return handleUpload('qcddForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $qcddFiles = Storage::disk('public')->files('QCDD');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($qcddFiles) && count($qcddFiles) > 0)

                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="qcdd">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($qcddFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="qcdd"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>


                <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" placeholder="Search by filename" data-container="qcdd">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="qcdd">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">


                <div class="row mt-2 file-card-container" id="qcdd">

                    @php
                        $qcddFiles = Storage::disk('public')->files('QCDD');
                        $rowCount = 0;
                    @endphp

                    @foreach($qcddFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="rh" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="rhForm" action="{{ route('admin.upload.rh') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'rh')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainerrh" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'Recrusos Humanos')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtonrh" class="upload-button bg-danger" onclick="return handleUpload('rhForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $rhFiles = Storage::disk('public')->files('RH');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($rhFiles) && count($rhFiles) > 0)

                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="rh">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($rhFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="rh"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>


                <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" placeholder="Search by filename" data-container="rh">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="rh">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">


                <div class="row mt-2 file-card-container" id="rh">

                    @php
                        $rhFiles = Storage::disk('public')->files('RH');
                        $rowCount = 0;
                    @endphp

                    @foreach($rhFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="empty" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="emptyForm" action="{{ route('admin.upload.empty') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'empty')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainerempty" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'Empty')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtonempty" class="upload-button bg-danger" onclick="return handleUpload('emptyForm', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $emptyFiles = Storage::disk('public')->files('Empty');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($emptyFiles) && count($emptyFiles) > 0)

                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="empty">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($emptyFiles as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="empty"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>


                <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" placeholder="Search by filename" data-container="empty">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="empty">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">


                <div class="row mt-2 file-card-container" id="empty">

                    @php
                        $emptyFiles = Storage::disk('public')->files('Empty');
                        $rowCount = 0;
                    @endphp

                    @foreach($emptyFiles as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

    </div>

    <div id="empty2" class="content">

        @auth

            @if(auth()->user()->hasRole('worker') || auth()->user()->hasRole('admin'))

                <form id="empty2Form" action="{{ route('admin.upload.empty2') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <label class="file-drop-area" id="fileDropArea">

                        <div class="file-icon">

                            <img src="{{asset('img/format_icons/default.png')}}" alt="File Icon" style="max-height: 6vh">

                        </div>

                        <input type="file" class="file-input" name="files[]" id="fileInput" multiple onchange="displaySelectedFiles(this, 'empty2')">

                        <span class="file-label">Click to Upload a file</span>

                        <h6 class="file-label" style="font-size: 1.3vh; color: grey">(supported files: PDF, EXCEL, POWERPOINT, WORD)</h6>

                    </label>


                    <div class=" mt-4">
                        <!-- Container for file cards -->
                        <div id="droppedFilesContainerempty2" class="row">

                        </div>

                        <hr id="simplehr" style="display: none;">

                        <!-- Warning container -->
                        <div id="warning-container" class="alert alert-danger mt-3" role="alert" style="display: none;">
                            <strong>Warning!</strong> Unsupported file can´t be uploaded, remove the file to proceed.
                        </div>

                        <!-- Mostra erro apenas da sreen que deu o erro de upload -->
                        @if(isset($errorMessage) && $screen == 'Empty2')
                            <div class="error-message">{{ $errorMessage }}</div>
                        @endif

                        <button id="uploadButtonempty2" class="upload-button bg-danger" onclick="return handleUpload('empty2Form', previewedFiles)" style="display: none;">Upload</button>

                    </div>

                </form>

            @endif

        @endauth

        @php
            $empty2Files = Storage::disk('public')->files('Empty2');
        @endphp

            <!-- File List Cards -->

        <h3 class="mt-4">Uploaded Files</h3>

        @if(!is_null($empty2Files) && count($empty2Files) > 0)

                <div class="container-fluid mt-4 mb-4">
                    <div class="uploader-checkboxes-box mb-4">
                        <h4 class="uploader-checkboxes-title">Uploaded By</h4>
                        <div class="uploader-checkboxes btn-group" role="group" aria-label="Uploader checkboxes" data-container="empty2">
                            @php
                                $uploaders = [];
                            @endphp
                            @foreach($empty2Files as $file)
                                @php
                                    $uploaderId = explode('_', pathinfo($file, PATHINFO_FILENAME))[0];
                                    $uploaderName = explode('_', pathinfo($file, PATHINFO_FILENAME))[1];
                                @endphp
                                @if(!in_array($uploaderName, $uploaders))
                                    <label class="btn btn-outline-primary rounded">
                                        <input type="checkbox" class="uploader-checkbox" value="{{ $uploaderName }}" data-container="empty2"> {{ $uploaderName }}
                                    </label>
                                    @php
                                        $uploaders[] = $uploaderName;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="container-fluid mt-4 mb-4">

                <input type="text" id="file-search" class="form-control mb-2" placeholder="Search by filename" data-container="empty2">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="sort-select">Sort by:</label>
                    </div>
                    <select class="custom-select" id="sort-select" data-container="empty2">
                        <option value="name" data-arrow="asc">Name</option>
                        <option value="date" data-arrow="asc">Upload Date</option>
                        <option value="format" data-arrow="asc">File Format</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary sort-arrow" type="button"><i class="fas fa-chevron-up"></i></button>
                    </div>
                </div>

            </div>

            <div class="container-fluid scrollable-div m-1" style="max-height: 68vh; overflow-y: auto;">


                <div class="row mt-2 file-card-container" id="empty2">

                    @php
                        $empty2Files = Storage::disk('public')->files('Empty2');
                        $rowCount = 0;
                    @endphp

                    @foreach($empty2Files as $index => $file)
                        @if($rowCount % 6 == 0)
                        @endif

                        <div class="card file-card flex-fill position-relative m-2" style="border-radius: 15px; max-width: 26vh">

                            <div class="card-header" style="height: 8vh; border-radius: 15px 15px 0 0">

                                <div class="card-title-container">

                                    <h5 class="card-title mb-1" style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">
                                        {{ substr(strrchr($file, "_"), 1) }}
                                    </h5>

                                    <h6 style="color: grey">.{{ pathinfo($file, PATHINFO_EXTENSION) }}</h6>

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column justify-content-end">

                                <p class="card-text" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Uploaded By: {{ explode('_', pathinfo($file, PATHINFO_FILENAME))[1] }}</p>

                                <p class="card-text" style="margin-bottom: 0">Uploaded At: {{ date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)) }}</p>

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

                                @auth

                                    @if(auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.delete.file') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="filePath" value="{{ $file }}">
                                            <button type="submit" class="btn btn-danger btn-block delete-btn mt-1">Delete</button>
                                        </form>
                                    @endif

                                @endauth

                            </div>

                        </div>

                        @php
                            $rowCount++;
                        @endphp

                    @endforeach

                </div>

            </div>

        @else
            <div class="alert alert-danger mt-4" role="alert">
                No files uploaded yet.
            </div>
        @endif

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

        <!-- ############################## File Display before uploading ############################# -->

        // Function to display selected files when using file input
        function displaySelectedFiles(input, screenContext) {
            const newFiles = input.files;

            // Update the global files array
            files = Array.from(newFiles);

            // Display dropped files based on the screen context
            displayDroppedFiles(files, screenContext);
        }

        let previewedFiles = []; // Array to store the files that have been previewed

        function displayDroppedFiles(files, screenContext) {
            // Get the dropped files container using the screenContext
            const droppedFilesContainer = document.getElementById('droppedFilesContainer' + screenContext);

            // Clear previous files from the container
            droppedFilesContainer.innerHTML = '';

            // Reset previewedFiles array
            previewedFiles = [];

            // Display file cards with improved design and margin between them
            files.forEach((file, index) => {
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

                // Add the file to the previewedFiles array
                previewedFiles.push({
                    name: file.name,
                    size: file.size
                });

                // Card content
                card.innerHTML =
                    `
        <div class="d-flex justify-content-end"> <!-- Align delete button to the right -->
                    <button type="button" style="color: grey; text-decoration: none;" class="btn btn-link delete-btn position-absolute mt-0 delbuttoncard" onclick="deleteDisplayedFile('${index}', '${screenContext}')">&times;</button>

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

            // Show upload button
            const uploadButton = document.getElementById('uploadButton' + screenContext);
            const simplehr = document.getElementById('simplehr');
            if (files.length > 0) {
                uploadButton.style.display = 'block';
                simplehr.style.display = 'block';
            } else {
                uploadButton.style.display = 'none';
                simplehr.style.display = 'none';
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
                    return '<img src="{{ asset("img/format_icons/excel.png") }}" alt="XLSX" style="width: 30px; height: 30px;">'; // XLSX and XLSM file icon
                case 'pptx':
                    return '<img src="{{ asset("img/format_icons/powerpoint.png") }}" alt="PPTX" style="width: 30px; height: 30px;">'; // PPTX file icon
                default:
                    return '<img src="{{ asset("img/format_icons/errorfile.png") }}" alt="File" style="width: 40px; height: 40px;">'; // Default file icon
            }
        }

        /*

        function deleteDisplayedFile(index, screenContext) {
            console.log('Deleting file at index:', index); // Debugging statement

            // Remove the file from the files array
            const deletedFile = files.splice(index, 1)[0]; // Remove the file at the specified index and store it

            // Re-display dropped files after deletion
            displayDroppedFiles(files, screenContext);

            // Get the input element
            const input = document.getElementById('fileInput'); // Assuming 'fileInput' is the ID of your input element

            // Create a new FileList without the deleted file
            const newFiles = Array.from(input.files).filter((file, idx) => idx !== index);

            // Create a new input element
            const newInput = document.createElement('input');
            newInput.type = 'file';
            newInput.className = 'file-input';
            newInput.name = 'files[]';
            newInput.id = 'fileInput';
            newInput.multiple = true;
            newInput.onchange = function() {
                displaySelectedFiles(this, screenContext);
            };

            // Set the new FileList to the new input element
            newInput.files = newFiles.length > 0 ? newFiles : null;

            // Replace the old input element with the new one
            input.parentNode.replaceChild(newInput, input);
        }

        */

        let deletedFilesIndexes = [];

        function deleteDisplayedFile(index, screenContext) {
            // Remove the file from the previewedFiles array
            previewedFiles.splice(index, 1);

            console.log(previewedFiles); // Debugging statement

            // Redisplay the files with the updated array
            displayDroppedFiles(previewedFiles, screenContext);
        }

        function handleUpload(formId, previewedFiles) {
            const form = document.getElementById(formId);

            // Create a hidden input field to store the filenames
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'previewedFiles';
            hiddenInput.value = JSON.stringify(previewedFiles);

            // Append the hidden input field to the form
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }

        <!-- ############################## End File Display before uplaoding ############################# -->

        <!-- ################################## File Upload ################################## -->

        /*

        // Function to handle the upload
        function handleUpload(formId) {
            if (files.length > 0) {
                uploadFiles(formId, files); // Call uploadFiles with the form ID and selected files
            } else {
                console.error('No files have been selected.');
            }
        }

        // Function to upload files
        function uploadFiles(formId, files, screenContext) {
            console.log('Uploading files from:', screenContext); // Log the screen context for debugging

            // Get the list of files from the droppedFilesContainer
            const droppedFilesContainer = document.getElementById('droppedFilesContainer' + screenContext);
            const fileElements = droppedFilesContainer.querySelectorAll('.card-title'); // Assuming card titles contain file names

            // Extract file names from file elements
            const fileNames = Array.from(fileElements).map(element => element.textContent.trim());

            // Filter the files array based on file names in the droppedFilesContainer
            const filesToUpload = Array.from(files).filter(file => fileNames.includes(file.name));

            console.log('Files to upload:', filesToUpload); // Log files to be uploaded for debugging

            const form = document.getElementById(formId); // Get the form dynamically using formId
            const formData = new FormData(form);

            // Append each file to the FormData object
            filesToUpload.forEach(file => {
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

        */

        <!-- ################################ End File Upload ################################ -->

        <!-- ################################## File Search ################################## -->

        $(document).ready(function() {
            // Define an object to store initial files for each container
            var initialFiles = {};

            // Function to handle file search for a specific container
            $('#file-search, #file-search2').on('input', function() {
                var searchText = $(this).val().toLowerCase().trim(); // Get search input text and convert to lowercase
                var containerId = $(this).data('container'); // Get the container ID from data attribute

                // Get the file card container for the specified ID
                var fileCardContainer = $('#' + containerId);

                if (!(containerId in initialFiles)) {
                    // If the initial files for this container have not been stored yet, store them now
                    initialFiles[containerId] = fileCardContainer.html();
                }

                // Iterate over each file card within the container
                $('.card', fileCardContainer).each(function() {
                    var fileName = $(this).find('.card-title.mb-1').text().toLowerCase(); // Get the file name and convert to lowercase
                    if (fileName.includes(searchText)) {
                        // Remove the hidden class to show the file card if the search text is found in the file name
                        $(this).removeClass('hidden');
                        fileCardContainer.removeClass('hidden'); // Show the file card container
                    } else {
                        // Add the hidden class to hide the file card if the search text is not found
                        $(this).addClass('hidden');
                        // Remove the col-md-2 div from the DOM
                        $(this).closest('.col-md-2').remove();
                    }
                });

                // Hide the file card container if no files match the search
                if (!$('.card', fileCardContainer).not('.hidden').length) {
                    fileCardContainer.addClass('hidden');
                } else {
                    fileCardContainer.removeClass('hidden');
                }
            });
        });

        <!-- ################################ End File Search ################################ -->

        <!-- ################################## File Sort ################################## -->

        $(document).ready(function() {
            // Click event for arrow
            $('.sort-arrow').on('click', function() {
                var arrow = $(this).find('i');
                var currentArrow = arrow.hasClass('fa-chevron-up') ? 'asc' : 'desc';
                var sortBy = $(this).parent().siblings('.custom-select').val(); // Get the selected sorting criteria
                var containerId = $(this).parent().siblings('.custom-select').data('container'); // Get the container ID from data attribute

                // Toggle arrow direction
                arrow.toggleClass('fa-chevron-up fa-chevron-down');

                // Call the function to sort files
                sortFiles(containerId, sortBy, currentArrow);
            });

            // Change event for select
            $('.custom-select').on('change', function() {
                var sortBy = $(this).val(); // Get the selected sorting criteria
                var currentArrow = $(this).siblings('.sort-arrow').find('i').hasClass('fa-chevron-up') ? 'asc' : 'desc'; // Get the current sorting direction
                var containerId = $(this).data('container'); // Get the container ID from data attribute

                // Call the function to sort files
                sortFiles(containerId, sortBy, currentArrow);
            });

            function sortFiles(containerId, sortBy, arrow) {
                var fileCardContainer = $('#' + containerId).find('.file-card-container'); // Get the file card container within the specified container

                console.log("File Card Container:", fileCardContainer);

                // Get the file cards within the container
                var files = $('.card', fileCardContainer);

                console.log("Files:", files);

                // Sort the file cards based on the selected sorting criteria
                files.sort(function(a, b) {
                    var aValue, bValue;

                    switch (sortBy) {
                        case 'name':
                            aValue = $(a).find('.card-title.mb-1').text().toLowerCase();
                            bValue = $(b).find('.card-title.mb-1').text().toLowerCase();
                            break;
                        case 'date':
                            aValue = $(a).find('.card-text').text().split('Uploaded At: ')[1];
                            bValue = $(b).find('.card-text').text().split('Uploaded At: ')[1];
                            break;
                        case 'format':
                            aValue = $(a).find('.mt-4 img').attr('alt').toLowerCase();
                            bValue = $(b).find('.mt-4 img').attr('alt').toLowerCase();
                            break;
                        default:
                            aValue = $(a).text().toLowerCase();
                            bValue = $(b).text().toLowerCase();
                            break;
                    }

                    if (sortBy !== 'date') {
                        return aValue.localeCompare(bValue);
                    } else {
                        // For dates, convert to timestamp for comparison
                        return new Date(aValue).getTime() - new Date(bValue).getTime();
                    }
                });

                // Reverse the order if arrow is pointing down (descending)
                if (arrow === 'desc') {
                    files = files.toArray().reverse();
                }

                console.log("Sorted Files:", files);

                // Re-append the sorted file cards back to the file-card-container
                fileCardContainer.empty().append(files);
            }


        });

        <!-- ################################ End File Sort ################################ -->

        <!-- ################################## Filter file by name of uploaded by ################################ -->

        // Get all checkboxes for uploader's name
        const uploaderCheckboxes = document.querySelectorAll('.uploader-checkbox');

        // Add event listener to each checkbox
        uploaderCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const checkedUploaders = Array.from(uploaderCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value.toLowerCase());

                // Get the ID of the container to filter
                const containerId = checkbox.getAttribute('data-container');

                // Get the container element by its ID
                const container = document.getElementById(containerId);

                // Get all file cards within the specified container
                const fileCards = container.querySelectorAll('.file-card');

                // Iterate over file cards within the container
                fileCards.forEach(function(fileCard) {
                    const uploaderName = fileCard.querySelector('.card-body .card-text').textContent.trim().toLowerCase().split(':')[1].trim();
                    const shouldShow = checkedUploaders.length === 0 || checkedUploaders.some(uploader => uploaderName.includes(uploader));
                    fileCard.style.display = shouldShow ? '' : 'none';
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.uploader-checkbox');

            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        this.parentNode.classList.add('active');
                    } else {
                        this.parentNode.classList.remove('active');
                    }
                });
            });
        });

        <!-- ################################ End Filter file by name of uploaded by ############################## -->

    </script>

    </body>

    </html>

@endif

@endsection
