@extends('master/main')

@section('css_block')
  <style type="text/css">
    .dropdown-wrapper {
      padding-left: 0%;
      padding-right: 0%;
      height: 30px;
      background: white;
      border-radius: 0px;
    }
    .dropdown-content {
      width: 100%;
      height: 30px;
      padding: 0% 0% 0% 5%;
      border-radius: 0px;
      border-color: white;
      text-align: left;
    }
    .dropdown-content:hover, .dropdown-content:focus {
      background-color: white !important;
      border-color: white !important;
    }
    .dropdown-list-wrapper {
      width: 100%;
      margin-top: -1%;
      padding-top: 0%;
      padding-bottom: 0%;
      border-radius: 0px;
      background-color: rgba(121, 121, 121, 0.85);
    }
    .dropdown-list-wrapper > li {
      color: white;
      padding:1% 0;
    }
    .dropdown-list-wrapper > li:hover {
      color: white;
      background-color: rgb(54, 54, 54);
      padding:1% 0;
    }
    .dropdown-list-wrapper > li  > div > label > input[type="checkbox"] {
      -ms-transform: scale(1.3); /* IE */
      -moz-transform: scale(1.3); /* FF */
      -webkit-transform: scale(1.3); /* Safari and Chrome */
      -o-transform: scale(1.3); /* Opera */
    }
    .checkbox {
      margin-left: 5%;
    }
    .dropdown-toggle > .pull-right {
      float: right !important;
      padding-right: 10px;
    }
  </style>
@stop

@section('js_block')
  <script src="{{ asset('legacy/js/explore.js') }}"></script>
  <script>
    window.init_data = {
      rootContainer: {!! json_encode($root_container, JSON_NUMERIC_CHECK) !!},
      statuses: {!! json_encode($statuses, JSON_NUMERIC_CHECK) !!},
      map_options : [
        @foreach (config("argodf.map_options") as $option => $enable)
        {
          name: "{{ trans("form.explore.search_bar.$option") }}",
          value: "{{ $option }}",
          enable: {{ ($enable) ? true: 0 }},
        },
        @endforeach
      ]
    };
    window.lang_trans = {
      search_bar_map_options_title: "{{ trans('form.explore.search_bar.map_options') }}",
      search_bar_region: "{{ trans('form.explore.search_bar.region') }}",
      search_bar_district: "{{ trans('form.explore.search_bar.district') }}",
      search_bar_more: "{{ trans('form.explore.search_bar.more') }}",
      search_bar_search_project_by_title: "{{ trans('form.explore.search_bar.search_projects_by_title') }}",
      project_list_of_map_loading_projects: "{{ trans('form.explore.loading_projects') }}",
    }
  </script>
@stop

@section('content')
  <div class="explore" ng-app="ExploreApp" ng-controller="ExploreIndexCtrl">
    <search-bar class="search-bar"
      mode="mode"
      map="map"
      container-name="rootContainer.name"
      projects="projects"
      filters="filters"
      statuses="statuses"
      regions="regions"
      conditions="conditions"
      show-advance-box="showAdvanceBox"
      map-options="mapOptions"
      search-projects="searchProjects()"
      enabling-fields-length="enabling_fields_length"
      lang="lang"
    >
    </search-bar>

    <project-list ng-show="mode == 'list'" ng-click="showAdvanceBox=false" class="hidden-xs project-list"
      mode="mode"
      container-name="rootContainer.name"
      projects="projects"
      conditions="conditions"
      filters="filters"
      total-items="total_items"
      current-page="current_page"
      projects-per-page="projects_per_page"
      show-regions="show_regions"
      search-projects="searchProjects()"
    ></project-list>

    <project-list-mobile class="visible-xs project-list-mobile" ng-show="mode == 'list'"
      mode="mode"
      container-name="rootContainer.name"
      projects="projects"
      conditions="conditions"
      filters="filters"
      total-items="total_items"
      current-page="current_page"
      projects-per-page="projects_per_page"
      search-projects="searchProjects()"
    ></project-list-mobile>

    <div class="row" ng-show="mode == 'map'" ng-click="showAdvanceBox=false">
      <div class="col-sm-4 hidden-xs projects-list-of-map-col">
        <project-list-of-map class="project-list-of-map"
          projects="projects"
          container-name="rootContainer.name"
          conditions="conditions"
          order-by="orderBy"
          filters="filters"
          search-processing="searchProcessing"
          total-items="total_items"
          current-page="current_page"
          projects-per-page="projects_per_page"
          search-projects="searchProjects()"
          marker-popup-switch="markerPopupSwitch"
          lang="lang"
          >
        </project-list-of-map>
      </div>
      <div class="col-sm-8 projects-map-col">
        <mode-switch
          class="hidden-xs mode-switch"
          mode="mode">
        </mode-switch>
        <leaflet
          center="map.center"
          defaults="map.defaults"
          markers="map.markers"
          event-broadcast="map.events"
          paths="map.paths"
          layers="map.layers"
          width="100%"
          height="750px">
        </leaflet>
        <div class="speedLegend" ng-show="map.layers.overlays.tracker.visible">
            <table class="table table-bordered">
                <tr>
                    <td>0-19</td>
                    <td>20-39</td>
                    <td>40-59</td>
                    <td>60-79</td>
                    <td>80-99</td>
                </tr>
                <span>Map Key (km/h)</span>
            </table>
        </div>
      </div>
    </div>
    <project-list-of-map-mobile class="visible-xs project-list-of-map-mobile"
      ng-show="mode == 'map'"
      container-name="rootContainer.name"
      projects="projects"
      filters="filters"
      total-items="total_items"
      conditions="conditions"
      search-projects="searchProjects()"
    >
    </project-list-of-map-mobile>
  </div>
@stop
