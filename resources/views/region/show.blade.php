@extends('master/main')

@section('js_block')
  <script>
    window.init_data = {
      regionId: {{ $regionId }}
    };
  </script>

  <script src="{{ asset('legacy/js/region.js') }}"></script>

@stop

@section('content')
<div ng-app="RegionApp" ng-controller="RegionShowCtrl">
  <div class="container header-container">

    <div class="row title-row">
      <div class="col-md-12">
        <h1 ng-bind="regionTitle"></h1>
      </div>
    </div>
  </div>
  <div class="header-line"></div>
  <div class="container region-show">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 maincontent-container">

      <div class="row" ng-if="subRegions.length > 0">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <subregion-list
           class="subregion-list"
           subregion-label="subregionLabel"
           data="subRegions"></subregion-list>
        </div>
      </div>

      <div class="row" ng-if="projects.length > 0">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <project-list-by-region
           class="project-list"
           data="projects"></project-list-by-region>
        </div>
      </div>
    </div>
  </div>
</div>
@stop
