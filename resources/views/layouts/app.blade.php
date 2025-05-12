<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
	<base href="../../">
	<title>Review Scrapping Bukalapak - {{ucwords(Request::segment(1))}}</title>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta charset="utf-8" />
	<meta name="description"
		content="Review Scrapping Bukalapak" />
	<meta name="keywords"
		content="Review Scrapping Bukalapak" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title"
		content="Review Scrapping Bukalapak" />
	<meta property="og:url" content="{{url('/')}}" />
	<meta property="og:site_name" content="Review Scrapping Bukalapak" />
	<link rel="canonical" href="{{url('/')}}" />
	<link rel="shortcut icon" href="{{url('assets/media/bl.png')}}" />
	<!--begin::Fonts-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
	<!--end::Fonts-->
	<!--begin::Page Vendor Stylesheets(used by this page)-->
	
	<!--end::Page Vendor Stylesheets-->
	<!--begin::Global Stylesheets Bundle(used by all pages)-->
	<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
	@yield('pages-style')
	<!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed"  style="display: none">
	<!--begin::Main-->
	<!--begin::Root-->
	<div class="d-flex flex-column flex-root">
		<!--begin::Page-->
		<div class="page d-flex flex-row flex-column-fluid">

			@include('layouts.sidebar')

			<!--begin::Wrapper-->
			<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

				@include('layouts.header')

				<!--begin::Content-->
				@yield('content')
				<!--end::Content-->

				@include('layouts.footer')

			</div>
			<!--end::Wrapper-->
		</div>
		<!--end::Page-->
	</div>
	<!--end::Root-->

	<!--begin::Javascript-->
	<script>
		var hostUrl = "assets/";
	</script>
	<!--begin::Global Javascript Bundle(used by all pages)-->
	<script src="assets/plugins/global/plugins.bundle.js"></script>
	<script src="assets/js/scripts.bundle.js"></script>
	<!--end::Global Javascript Bundle-->
	<!--begin::Page Vendors Javascript(used by this page)-->
	<!--end::Page Vendors Javascript-->
	<!--begin::Page Custom Javascript(used by this page)-->
	<script src="assets/js/widgets.bundle.js"></script>
	<!--end::Page Custom Javascript-->
	<!--end::Javascript-->
	@yield('pages-script')
</body>
<!--end::Body-->
<script>
	$(document).ready(function(){
		let token = sessionStorage.getItem('token');
		//console.log(window.location);
		var urlnya = window.location.origin;
    $.ajax({
		url: urlnya+'/'+'api/validated-token',
        context: document.body,
		data: {token},
		type: 'POST',
		dataType: 'json',
        success: function (response){
			if (!response.data) {
				location.href = '/login';
			} else {
				$('#kt_body').show();
			}
        }
    });
});
</script>
<script>
	// $.ajaxSetup({
    // headers: {
    //     let token = JSON.parse(sessionStorage.getItem('token', response.token));
    // 	}
	// });
	let token = sessionStorage.getItem('token');
	$.ajaxSetup({
    beforeSend: function(xhr) {
        xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    }
	});

	$("#logOut").click(function(){
	var urlnya = window.location.origin;
                   
  	$.ajax({
  		url: urlnya+'/'+'api/logout',
		type: 'POST',
		dataType: 'json',
		// data: {token, }
		success: function(response){
			window.location.href = '/login';
  		}});
	});
</script>
<script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script type="text/javascript">
		@if($message=Session::get('success'))
			toastr.success("{{ $message }}");
		@endif
		@if($message=Session::get('error'))
			toastr.error("{{$message}}");
		@endif
	</script>
</script>
</html>