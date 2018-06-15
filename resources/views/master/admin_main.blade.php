<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ trans('site.title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
    <link href="{{ asset("favicon.ico") }}" rel="icon" type="image/x-icon" />

    <link media="all" type="text/css" rel="stylesheet" href="{{ asset('legacy/css/components.css').'?'.config('argodf.reload_hash') }}">
    <link media="all" type="text/css" rel="stylesheet" href="{{ asset('legacy/css/admin-main.css').'?'.config('argodf.reload_hash') }}">

@yield('css_block')

<script src="{{ asset('legacy/js/components.min.js').'?'.config('argodf.reload_hash') }}"></script>
<script src="{{ asset('legacy/js/service.js').'?'.config('argodf.reload_hash') }}"></script>

<script>
    window.constants = {
      CSRF_TOKEN : '{{ csrf_token() }}',
      ABS_URI : '{{ asset('/') }}',
      DATE_FORMAT : {!! json_encode(Config::get('argodf.ng_date_format')) !!},
      DATETIME_FORMAT : {!! json_encode(Config::get('argodf.ng_datetime_format')) !!},
      INNER_MAP_CENTER: {!! json_encode(Config::get('argodf.inner_map_center'), JSON_NUMERIC_CHECK) !!}
    };
</script>

@yield('js_block')
</head>
<body>

<div class="container-fluid">
    <div id="overall-header" class="row border-bottom">
        <div id="overall-nav" class="col-md-offset-1 col-md-10 border-left-right">
            <div id="overall-title" class="col-xs-12 col-sm-8 col-md-8">
                {{ trans('site.title') }}
            </div>
            <div id="overall-links" class="col-xs-12 col-sm-4 col-md-4 pull-right">
                <a class="link" href="{{ asset('/') }}">Back To Site</a>
                <a class="link">{{ Auth::user()->name }}</a>
            </div>
        </div>
    </div>
</div>

@yield('content')

</body>
</html>
