@extends('master/main')

@section('extra_title', isset($extraTitle) ? ' - '.$extraTitle : '')


@section('css_block')
  <style>
    .subproject.comment a{
      background-color: #fff500!important;
    }
    .subproject.comment a.active{
      color: #606060!important;
      background-color: #f8df04!important;
    }
  </style>
@stop

@section('js_block')
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
    window.init_data = {
      project_id: {{ $project_id }},
    };
  </script>

  <script src="{{ asset('legacy/js/attach-uploader.js') }}"></script>
  <script src="{{ asset('legacy/js/exporting.js') }}"></script>
  <script src="{{ asset('legacy/js/argo_map.js') }}"></script>
  <script src="{{ asset('legacy/js/project.js') }}"></script>
@stop

@section('content')
<div ng-app="ProjectApp" ng-controller="ShowCtrl">
  <div class="container header-container">
    <div class="row breadcrumb-row">
      <div class="col-md-12">
        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
          @foreach($breadcrumbs as $index => $breadcrumb)
            <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
              <a href="{{ array_get($breadcrumb, 'url', '#') }}" itemprop="item">
                <span itemprop="name">
                  {{ array_get($breadcrumb, 'title') }}
                </span>
              </a>
              <meta itemprop="position" content="{{ $index + 1 }}" />
            </li>
          @endforeach
        </ol>
      </div>
    </div>
    <div class="row title-row">
      <div class="col-md-12">
        <h1 ng-bind="project_title"></h1>
        <h1><small ng-bind="regions.join(', ')"></small></h1>
      </div>
    </div>
  </div>
  <div class="header-line"></div>
  <div class="container project-show">
    <div class="row">
      <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 submenu-container">
        <div class="submenu">
          <nav class="navbar navbar-default" role="navigation" style="min-height: initial;">
            <div class="side-menu-container">
                <ul class="nav navbar-nav">
                  <li class="subproject"><a ng-class="{ active: submenu_pointer === null}" href="#" ng-click="showForm(null)">Basic Info</a></li>
                  <li class="subproject"><a ng-class="{ active: submenu_pointer === 'aggregated_data'}" href="#" ng-click="showAggregatedData()">Insights</a></li>
                  <li class="subproject comment"><a ng-class="{ active: submenu_pointer === 'comment'}" href="#" ng-click="showComment()">Comment</a></li>
                  <li class="subproject" ng-repeat="subcontainer in subcontainers"><a ng-href="<%subcontainer.path%>" ng-bind="subcontainer.name"></a></li>
                  <li class="item" ng-repeat="form in forms"><a href="#" ng-click="showForm(form.id)" ng-class="{ active: submenu_pointer==form.id}" ng-bind="form.name"></a></li>
                </ul>
            </div><!-- side-menu-container -->
          </nav>
        </div>
      </div>
      <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 maincontent-container">
        <div class="row" ng-show="submenu_pointer === null">
          <basic-info data="basic_info">
          </basic-info>
        </div>

        <div class="row" ng-show="submenu_pointer == 'aggregated_data'">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <aggregated-data data="aggregated_data">
            </aggregated-data>
          </div>
        </div>

        <div class="row" ng-show="submenu_pointer == 'comment'">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <iframe src="{{ asset('/comment/project/'.$project_id) }}" style="width: 100%; height: 500px; display: block;">
              <p>Your browser does not support iframes.</p>
            </iframe>
          </div>
        </div>

        <div class="row" ng-if="form.slider.length > 0">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <report-slider
             class="report-slide"
             form-name="form.name"
             data="form.slider"
             noreport-string = "{{ Lang::get('form.project.component.no_report') }}"></report-slider>
          </div>
        </div>

        <div class="row" ng-if="form.values.length > 0">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
            <form-data-wrap class="form-datas-wrap row"
             data="form.values"
             title-gps-avgspeed="{{ Lang::get('form.project.field.gps.avg_speed') }}"
             title-gps-distance="{{ Lang::get('form.project.field.gps.distance') }}"
             title-gps-start-time="{{ Lang::get('form.project.field.gps.start_time') }}"
             title-gps-end-time="{{ Lang::get('form.project.field.gps.end_time') }}"
             ></form-data-wrap>
          </div>
        </div>

        <div class="row" ng-show="highchartObjects.length > 0 && !submenu_pointer">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
            <project-indicator
             class="project-chart"
             project-id="project_id"
             indicator-ids="indicator_ids"
             highchart-objects="highchartObjects"></project-indicator>
          </div>
        </div>

        <div class="row" ng-if="subcontainers.length > 0 && !submenu_pointer">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <container-list
             class="container-list"
             data="subcontainers"></container-list>
          </div>
        </div>

        <div class="row" ng-if="!submenu_pointer">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <argo-map
             class="project-map"
             data="map"></argo-map>
          </div>
        </div>

        <div class="row" ng-if="attachments.length > 0 && !submenu_pointer">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <project-attachment class="project-attachment"
             title="{{ Lang::get('form.attachment.file') }}"
             data="attachments"></project-attachment>
          </div>
        </div>

        <div class="row" ng-if="status_info && submenu_pointer === null">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
            <status-info class="project-info"
              data="status_info"
              title="{{ Lang::get('form.project.component.status') }}"
            >
            </status-info>
          </div>
        </div>

      </div>
    </div>



</div>
@stop
