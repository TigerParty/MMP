<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{ trans('site.title') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @if(config('argodf.head_meta', false))
    @include(config('argodf.head_meta'))
  @endif
  <link href="https://fonts.googleapis.com/css?family=Lato:400,700" rel="stylesheet" type="text/css">
  <link href="{{ asset("favicon.ico") }}" rel="icon" type="image/x-icon" />
  <link media="all" type="text/css" rel="stylesheet" href="{{ asset('legacy/css/components.css').'?'.config('argodf.reload_hash') }}">
  <link media="all" type="text/css" rel="stylesheet" href="{{ asset('legacy/css/main.css').'?'.config('argodf.reload_hash')  }}">

  <script src="{{ asset('legacy/js/components.min.js').'?'.config('argodf.reload_hash') }}"></script>
  <script src="{{ asset('legacy/js/service.js').'?'.config('argodf.reload_hash') }}"></script>

</head>

<body>
  <header class="tutorial homepage-header" >
    <div class="container">
      <div class="logo-section">
        <a href="/" class="link">
          <img src="{{ asset(session()->get('header_logo')) }}" class="logo img-responsive">
        </a>
        <div class="title">
          <span class="main">{{ trans('site.shorthead_title') }}</span>
        </div>
      </div>
      <div class="argo-nav-toggle">
        <input type="checkbox">
        <span class="hambur-line"></span>
        @include('master/navbar_items')
      </div>
    </div>
  </header>
  <div id="how-it-work-app" class="section">
    <div class="container">
      <h3 class="title" style="margin-left: 0">
        <span>{{ trans('site.how_it_work.download_app.title.line1') }}</span>
        <span>{{ trans('site.how_it_work.download_app.title.line2') }}</span>
      </h3>
      <div class="row">
        <div class="col-xs-12 col-sm-offset-2 col-sm-8 col-md-offset-2 col-md-8 col-lg-offset-4 col-lg-4 item">
          <div class="step-block download-btn">
              <div class="circle">1</div>
              <a href="{{ config('argodf.google_market_link')? config('argodf.google_market_link'): asset('apk/argo.apk') }}" target="_blank" class="ar-button-dnld">

                <object data="{{ asset('/images/icon/download.svg') }}" type="image/svg+xml" class="icon_dnld"></object>
                <p>Download App</p>
              </a>
              <p style="width: 70%"> Download and install the app from your mobile phone</p>
          </div>
        </div>
      </div>
      <div class="row">
          <div class="col-sm-offset-0 col-md-offset-1 col-lg-offset-1 col-xs-6 col-sm-4  col-md-2 col-lg-2 item" >
            <div class="step-block" >
                <div class="circle">2</div>
                <img src="{{ asset(trans('site.how_it_work.download_app.tutorial.step_2.img')) }}">
                <p>{{ trans('site.how_it_work.download_app.tutorial.step_2.description') }}</p>
            </div>
          </div>
          <div class="col-xs-6 col-sm-4  col-md-2 col-lg-2 item" >
            <div class="step-block" >
                <div class="circle">3</div>
                <img src="{{ asset(trans('site.how_it_work.download_app.tutorial.step_3.img')) }}">
                <p>{{ trans('site.how_it_work.download_app.tutorial.step_3.description') }}</p>
            </div>
          </div>
          <div class="col-xs-6 col-sm-4  col-md-2 col-lg-2 item" >
            <div class="step-block" >
                <div class="circle">4</div>
                <img src="{{ asset(trans('site.how_it_work.download_app.tutorial.step_4.img')) }}">
                <p>{{ trans('site.how_it_work.download_app.tutorial.step_4.description') }}</p>
            </div>
          </div>
          <div class="col-xs-6 col-sm-4  col-md-2 col-lg-2 item" >
            <div class="step-block" >
                <div class="circle">5</div>
                <img src="{{ asset(trans('site.how_it_work.download_app.tutorial.step_5.img')) }}">
                <p>{{ trans('site.how_it_work.download_app.tutorial.step_5.description') }}</p>
            </div>
          </div>
          <div class="col-xs-6 col-sm-4  col-md-2 col-lg-2 item" >
            <div class="step-block" >
                <div class="circle">6</div>
                <img src="{{ asset(trans('site.how_it_work.download_app.tutorial.step_6.img')) }}">
                <p>{{ trans('site.how_it_work.download_app.tutorial.step_6.description') }}</p>
            </div>
          </div>
      </div>
    </div>
  </div>
  @include('master/footer')
</body>
</html>
