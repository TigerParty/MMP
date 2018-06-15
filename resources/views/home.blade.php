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

  @include('master/google_analytics')

  @if(config('services.fb_comment_plugin.app_id'))
    <script>
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.7&appId={{ config('services.fb_comment_plugin.app_id') }}";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
  @endif


  <script>
      window.constants = {
          ABS_URI : '{{ asset('/') }}',
          DATE_FORMAT : {!! json_encode(config('argodf.ng_date_format')) !!},
      };
  </script>
</head>

<body>
  <div id="navigator" class="container-fulid" style="background-image: url('{{ config('argodf.theme.navigator') }}')">
    <header class="homepage-header">
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
    <div class="container">
      <div class="row info-content">
        <div class="col-md-12">
          @foreach(trans('site.navigator.content') as $line)
            <p>{!! $line !!}</p>
          @endforeach
          <p></p>
          @if(config('argodf.citizen_sms_enabled'))
          <p><span class="glyphicon glyphicon-phone"></span>{{ trans('site.navigator.sms_intro.text') }} {{ trans('form.citizen_sms.number') }}</p>
          <a class="btn sms-btn" href="{{ asset('/feedback#/sms') }}">
            {{ trans('site.navigator.sms_intro.btn') }}
          </a>
          @endif
        </div>
      </div>
      <div class="row links">
        <a href="{{ (config('argodf.new_map_page_enabled')) ? asset('/explore') : asset('/map') }}" class="ar_button_home shadow col-xs-12 col-sm-6 col-md-3 col-lg-3">
          <p>{{ trans('site.navigator.quick_links.search.text') }}</p>
          <p>{{ $container_name }}S</p>
          <object data="{{ asset('/images/icon/search_icon.svg') }}" type="image/svg+xml" class="icon_search"></object>
        </a>
        <a href="{{ asset('/tutorial') }}" class="ar_button_home shadow col-xs-12 col-sm-6 col-md-3 col-lg-3">
          <p>{{ trans('site.navigator.quick_links.submit.text') }}</p>
          <p>{{ $container_name }}S</p>
          <object data="{{ asset('/images/icon/submit_icon.svg') }}" type="image/svg+xml" class="icon_submit"></object>
        </a>
      </div>
    </div>
  </div>
  <div id="project_recent" class="container-fulid section" ng-app="HomepageApp" ng-controller="HomepageCtrl as homeCtrl" >
    <h3 class="title">
      <span>
        {{ trans('site.recent_progress.title.line1') }}
        <br>
        {{ trans('site.recent_progress.title.line2') }}
      </span>
    </h3>
    <div class="container">
      <a class="btn btn-large pull-right veiw-all-btn " href="{{ asset('/explore') }}" >{{ trans('site.recent_progress.quick_links', ['name' => $container_name]) }}</a>
      <div class="row" style="margin-top: 60px">
        <div class="col-sm-12 col-md-4">
          <highchart id="chart1" config="homeCtrl.chartConfig"></highchart>

          <div class="chart-info" ng-show="homeCtrl.projectStatus.length>0">
            <div class="item" ng-repeat="status in homeCtrl.projectStatus" ng-if="status.total > 0">
            <p><% status.name%></p>
            <p><% status.percentage%>%</p>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-8">
          <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6" ng-repeat-start="project in homeCtrl.recentProjects">
              <article>
                <div class="article-thumbnail">
                  <a ng-href="<% project.projectURL %>"><img ng-src="<% project.coverImageURL %>" alt=""></a>
                </div>
                <div class="article-info">
                  <a ng-href="<% project.projectURL %>"><p class="article-subproject-title"><% project.title %></p></a>
                  <div class="article-info-detail">
                    <p>{{ trans('site.recent_progress.article.last_updated') }}:
                        <strong> <% project.updated_at | date %></strong>
                    </p>
                    <p ng-repeat="region in project.regions"><% region.label_name | capitalize %>:
                        <strong><% region.name %></strong>
                    </p>
                    <p>{{ trans('site.recent_progress.article.status') }}:
                        <strong><% project.project_status %></strong>
                    </p>
                  </div>
                </div>
              </article>
            </div>
            <div class="clearfix" ng-if="$index%2==1"></div>
            <div ng-repeat-end></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">
      <iframe src="{{ asset('/comment/home') }}" style="width: 100%; height: 300px; display: block;">
        <p>Your browser does not support iframes.</p>
      </iframe>
    </div>
  </div>

  @include('master/footer')
  <script src="{{ asset('legacy/js/homepage.js').'?'.config('argodf.reload_hash') }}"></script>
</body>
</html>
