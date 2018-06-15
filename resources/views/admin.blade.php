<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('site.title') }} @yield('extra_title')</title>
    <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <meta name="edit-permission" content="{{ argo_is_admin_accessible() }}">
    @include('master/head_meta')
    @include('master/google_analytics')
    <script type="text/javascript">
        window.constants = {
            BASE_URL: "{{ url('/') }}",
            PROAPP_URL: "{{ config('argodf.google_market_link')? config('argodf.google_market_link'): asset('apk/argo.apk') }}"
        }
    </script>
    </head>
    <body>
    <div id="admin"></div>
    <script src="/static/lang.js"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
