<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{ trans('site.title') }} @yield('extra_title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @if(Config::get('argodf.head_meta', false))
    @include(Config::get('argodf.head_meta'))
  @endif
  <link href="https://fonts.googleapis.com/css?family=Lato:400,700" rel="stylesheet" type="text/css">
  <link href="{{ asset("favicon.ico") }}" rel="icon" type="image/x-icon" />
  <link media="all" type="text/css" rel="stylesheet" href="{{ asset('legacy/css/components.css').'?'.config('argodf.reload_hash') }}">
  <link media="all" type="text/css" rel="stylesheet" href="{{ asset('legacy/css/main.css').'?'.config('argodf.reload_hash')  }}">
  <link media="all" type="text/css" rel="stylesheet" href="{{ asset( config('argodf.theme.css')) }}">
  @yield("css_block")

  <script src="{{ asset('legacy/js/components.min.js').'?'.config('argodf.reload_hash')  }}"></script>
  <script src="{{ asset('legacy/js/service.js').'?'.config('argodf.reload_hash')  }}"></script>

  <script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "Organization",
    "url": "{{ asset('/') }}",
    "name": "{{ trans('site.title') }}",
    "logo": "{{ asset(session()->get('header_logo')) }}",
    "description": "{{ trans('site.subtitle') }}"
  }
  </script>
  @include('master/google_analytics')
  <script>
      window.constants = {
          CSRF_TOKEN : '{{ csrf_token() }}',
          ABS_URI : '{{ asset('/') }}',
          DELETABLE :  {{ json_encode(argo_is_accessible(Config::get('argodf.delete_priority'))) }},
          DATETIME_FORMAT : {!! json_encode(Config::get('argodf.ng_datetime_format')) !!},
          DATE_FORMAT : {!! json_encode(Config::get('argodf.ng_date_format')) !!},
          HOME_MAP_CENTER : {!! json_encode(Config::get('argodf.home_map_center')) !!},
          INNER_MAP_CENTER : {!! json_encode(Config::get('argodf.inner_map_center')) !!},
          PROJECTS_PER_PAGE : {!! json_encode(Config::get('argodf.project_pagination.items_per_page')) !!},
          PROJECTS_INIT_PAGES : {!! json_encode(Config::get('argodf.project_pagination.init_pages')) !!}
      };
  </script>
  @yield("js_block")
</head>

<body>
  <!-- <div class="container"> -->
    @include('master/header')
    @yield("content")
  <!-- </div> -->

    @include('master/footer')

</body>
</html>
