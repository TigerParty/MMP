@extends('master/main')

@section('extra_title', isset($extraTitle) ? ' - '.$extraTitle : '')

@section('js_block')
  <script>
    window.init_data = {
      project_id: {!! $project_id !!},
      container_id: {!! $container_id !!}
    };
  </script>
  <script src="{{ asset('legacy/js/container.js') }}"></script>
@stop

@section('css_block')
@stop

@section('content')
<div ng-app="ContainerApp" ng-controller="ShowCtrl">
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
        <h1> <% container_name %> <br/><small><% regions.join(', ') %></small></h1>
      </div>
    </div>
  </div>
  <div class="header-line"></div>
  <div class="container project-show">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 maincontent-container">
      <div class="row" ng-if="subcontainers.length > 0">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <container-list
           class="container-list"
           data="subcontainers"></container-list>
        </div>
      </div>
      <div class="row" ng-if="subprojects.length == 0 && subcontainers.length == 0">
        <p style="text-align: center;">No <% container_name %> information to be displayed.</p>
      </div>
      <div class="row" ng-if="subprojects.length > 0">
        <div class="col-md-12">
          <subproject-list
           class="subproject-list"
           data="subprojects"
           filters="filters"
           container-name="container_name"
           ></subproject-list>
        </div>
      </div>

    </div>
  </div>
</div>
@stop
