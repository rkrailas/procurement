<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <!-- Dropzone -->
    <meta name='csrf-token' content="{{ csrf_token() }}">

    <title>P2P System</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('backend/dist/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- toastr Alert -->
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/plugins/toastr/toastr.min.css') }}">
    <!-- Pickup Date-Time -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('backend/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
 
    <!-- DataTables -->
    {{-- <link rel="stylesheet" href="{{ asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script> --}}

    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <!-- for pagination -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/1.9.2/tailwind.min.css" /> --}}
    <link rel="stylesheet" href="{{ asset('backend/plugins/tailwindcss1_9_2/tailwind.min.css') }}" />

    <!-- Dropzone -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/dropzone/dropzone.min.css') }}" type="text/css" />
    
<style>
        /* เปลี่ยนสี background ตรง Logo */
        .brand-link{
            background-color: #fff;
        }

        label {
            margin-bottom: 0px;
        }

        .row {
            margin-bottom: 8px;
        }
        
        
        .nissanTB th {
            /* white-space: nowrap; */
            background-color:#c3002f;
            color: white;
        }

        /* 
        .btn {
            background-color:rgb(221, 8, 8); 
            border: 0px;
        } */

        /* ทำให้มีเส้นใต้ Tab Header */
        /* .nav-tabs>.active>a, .nav-tabs>.active>a:hover, .nav-tabs>.active>a:focus {
            border-color: red;
            border-bottom-color: transparent;
        }
        .nav-tabs {
            border-bottom: 1px solid red;
        } */

        /* ทำให้ Tab content มีกรอบ */

        .tab-content {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
        }

        .my-card-header {
            font-size: 17px;
            padding-top: 5px;
            padding-bottom: 5px;
            padding-left: 5px;
            padding-right: 5px;
            background-color: #c3002f;
            color: white;
        }

        .my-card-body {
            padding-top: 5px;
            padding-bottom: 5px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .myGridTB :is(td,th) {
            padding-top: 5px;
            padding-bottom: 5px;
            padding-right: 2px;
            padding-left: 2px;
            margin: 0;
        }

        .myGridTB input{
            padding-right: 0;
        }

        .nav-icon-submenu{
            font-size: 1em;
            margin-left: 1em;
        }

        .btn-danger{
            background-color: #c3002f;
            border-color: white;
            /* border-radius: 7px; */
        }

        .btn-light{
            background-color: white;
            border-color: #ced4da;
            /* border-radius: 7px; */
            color: #c3002f;
        }

        hr {
            border: none;
            height: 3px;
            /* Set the hr color */
            color:darkgray; /* old IE */
            background-color: darkgray; /* Modern Browsers */
        }

        .custom-file-label,
        .custom-file-label::after {
            height: auto;
            padding-top: 1;
            padding-bottom: 1;
        }

        /* @font-face {
            font-family: NISSANBRAND;
            src: url('/fonts/NISSANBRAND-REGULAR.tff');
            src: url("{{asset('/fonts/NISSANBRAND-REGULAR.tff')}}");
        } */

    </style>
    
    @stack('styles')
    <livewire:styles />

</head>

<body class="hold-transition sidebar-mini text-sm">
    <div class="wrapper">

        <!-- Navbar -->
        @include('layouts.partials.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layouts.partials.aside')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            {{ $slot }}
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        @include('layouts.partials.footer')
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('backend/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('backend/dist/js/adminlte.min.js') }}"></script>
    <!-- toastr alert -->
    <script type="text/javascript" src=" {{ asset('backend/plugins/toastr/toastr.min.js') }}"></script>

    <!-- Pickup Date-Time -->
    {{-- <script type="text/javascript" src="https://unpkg.com/moment"></script> --}}
    <script type="text/javascript" src=" {{ asset('backend/plugins/tempusdominus-bootstrap-4/js/moment.js') }}"></script>
    <script type="text/javascript" src=" {{ asset('backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"
        integrity="sha512-HWlJyU4ut5HkEj0QsK/IxBCY55n5ZpskyjVlAoV9Z7XQwwkqXoYdCIC93/htL3Gu5H3R4an/S0h2NXfbZk3g7w=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}

    <!-- sweetalert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-popup-alert></x-popup-alert>
    <x-popup-success></x-popup-success>
    <x-delete-confirmation></x-delete-confirmation>
    <x-popup-image></x-popup-image>

    <!-- Dropzone -->
    <script src="{{ asset('backend/plugins/dropzone/dropzone.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            toastr.options = {
                "positionClass": "toast-bottom-right",
                "progressBar": true,
            }
        });

        window.addEventListener('alert', event => {
            toastr.success(event.detail.message, 'success!');
        })
    </script>

    <!-- toastr Message -->
    <script>
        window.addEventListener('display-Message', event => {
            toastr.success(event.detail.message, 'Success!');
        })
    </script>

    {{-- ป้องกันการกด Enter --}}
    <script type="text/javascript">
        window.document.onkeydown = CheckEnter;
        function CheckEnter(){
            if(event.keyCode == 13)
                return false;
            return true;
        }
    </script>

    @stack('js')
    <livewire:scripts />

</body>

</html>