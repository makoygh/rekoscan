<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'RekoScan') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- core css -->
        <link rel="stylesheet" href="../../../assets/vendors/core/core.css">

        <!-- layout css -->
        <link rel="stylesheet" href="../../../assets/css/demo2/style.css">

	<!-- Custom js for this page -->
    <script src="{{ asset('../assets/js/dashboard-dark.js') }}"></script>
	<!-- End custom js for this page -->

<!-- Plugin css for Data Tables -->
<link rel="stylesheet" href="{{ asset('../assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css') }}">
<!-- End plugin css for this page --> 

  <!-- Notifications -->  
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >

  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">    

  <link rel="shortcut icon" href="{{ asset('img/rekoscan_logo_icon.png') }}" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>


	<!-- core:js -->
	<script src="{{ asset('../assets/vendors/core/core.js') }}"></script>
	<!-- endinject -->
    
	<!-- inject:js -->
	<script src="{{ asset('../assets/vendors/feather-icons/feather.min.js') }}"></script>
	<script src="{{ asset('../assets/js/template.js') }}"></script>
	<!-- endinject -->
    
	<!-- Custom js for this page -->
    <script src="{{ asset('../assets/js/dashboard-dark.js') }}"></script>
	<!-- End custom js for this page -->    

<!-- Notifications -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

	<!-- Plugin js for Sweet Alert -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<script src="{{ asset('../assets/js/code.js') }}"></script>
	<!-- End custom js for this page -->

	<!-- Plugin js for data tables -->
	<script src="{{ asset('../assets/vendors/datatables.net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('../assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js') }}"></script>
	<script src="{{ asset('../assets/js/data-table.js') }}"></script>
	<!-- End custom js for this page -->	

        <script>
 @if(Session::has('message'))
 var type = "{{ Session::get('alert-type','info') }}"
 switch(type){
    case 'info':
    toastr.info(" {{ Session::get('message') }} ");
    break;

    case 'success':
    toastr.success(" {{ Session::get('message') }} ");
    break;

    case 'warning':
    toastr.warning(" {{ Session::get('message') }} ");
    break;

    case 'error':
    toastr.error(" {{ Session::get('message') }} ");
    break; 
 }
 @endif 
</script>	        

    </body>
</html>
