<!DOCTYPE html>
<html lang="en" id="service_htmlid">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ config('app.name', 'Laravel') }} Admin</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('sb_admin/css/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('sb_admin/css/sb-admin-2.css') }}" rel="stylesheet">
    <script src="{{ asset('sb_admin/js/moment.min.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <link rel="stylesheet"type="text/css"href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" />
    <link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css'rel='stylesheet'>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> --}}


    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>

<body id="page-top"{{ session()->has('lang_code') ? (session()->get('lang_code') == 'ar' ? 'style=direction:rtl' : '') : '' }}>
    <div id="wrapper">
        @include('layouts.admin.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                @include('layouts.admin.topbar')
                @yield('content')
            </div>
            @include('layouts.admin.footer')
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <a class="btn btn-primary" href="{{ url('/admin/logout') }}">{{ __('messages.logout') }}</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('sb_admin/js/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sb_admin/js/sb-admin-2.min.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
    <script src="{{ asset('sb_admin/js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('sb_admin/jquery-easing/jquery.easing.min.js') }}"></script>
    @yield ('footer_scripts')
    <script>
        function changeLanguage(lang) {
            window.location = '{{ url('change-language') }}/' + lang;
            var lang = $("#language_drop option:selected").val();
            // if(lang=='ar'){
            //     document.getElementById("page-top").style.direction = "rtl";

            // }
        }
    </script>
</body>
</html>
