<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('site.title') }} @yield('extra_title')</title>
  <link rel="stylesheet" href="{{ asset('css/comment.css') }}">
  <meta name="edit-permission" content="{{ argo_is_admin_accessible() }}">
  @include('master/head_meta')
  <script type="text/javascript">
    window.commentId = "{!! $id !!}"
    window.commentType = "{!! $type !!}"
  </script>
</head>
<body>
  <div id="commentApp"></div>
  <script src="/static/lang.js"></script>
  <script src="{{ asset('js/vendor.js') }}"></script>
  <script src="{{ asset('js/comment.js') }}"></script>
</body>
</html>
