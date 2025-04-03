<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{asset('assets/css/custome.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/stylesheet.css')}}">
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ asset('admin_assets/toastr/build/toastr.min.css') }}" rel="stylesheet">
</head>
<body>
@include('includes.header')
@yield('context')
<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script src="{{ asset('admin_assets/toastr/build/toastr.min.js') }}"></script>
<script src="{{ asset('admin_assets/toastr/toastr-init.js') }}"></script>
<script>
    function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
       
        $('#resumeInput').on('change', function() {
            var fileName = $(this).prop('files')[0].name;
            $('#fileName').html('<span style="color:#fff;">' + fileName + '</span>');
        });
        $('#portInput').on('change', function() {
            var fileName = $(this).prop('files')[0].name;
            $('#fileName').html('<span style="color:#fff;">' + fileName + '</span>');
        });
</script>
<!-- show action msg -->
<?php if(Session::get('msg')) { ?> 
<script type="text/javascript">
    $( document ).ready(function() {
      toastr.{!! session('type') !!}('{!! session('msg') !!}');
      setTimeout(function(){ 
      }, 500); 
    });   
</script>   
<?php } ?>
@yield('script')
</body>
</html>