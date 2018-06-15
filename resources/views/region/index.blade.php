@extends('master/main')

@section('css_block')
@stop

@section('js_block')
  <script>
    window.init_data = {
      rootRegions: {!! $rootRegions !!},
      rootRegionTitle: "{{ trans('site.shorthead_title') }}"
    };
  </script>
  <script src="{{ asset('legacy/js/region.js') }}"></script>
@stop

@section('content')
  <div class="container region-root" ng-app="RegionApp" ng-controller="RegionIndexCtrl">
    <div class="row">
      <div class="col-xs-offset-3 col-xs-6 col-xs-offset-3 col-sm-6 col-md-6 col-md-offset-1 map-block text-center">
        <svg width="80%" height="100%" viewBox="{{config('argodf.region_svg_viewBox')}}">
          <defs>
             <linearGradient id="ghana-gradient" x1="0%" y1="100%" x2="100%" y2="0%">
               <stop offset="30%" stop-color="#0066cc" />
               <stop offset="75%" stop-color="#008cc3" />
             </linearGradient>
          </defs>
          <defs>
            <clipPath id="map-paths">
              @foreach ($rootRegions as $region)
                @if ($region->map_path)
                    {!! $region->map_path !!}
                @endif
              @endforeach
            </clipPath>
          </defs>
          <rect x="0" y="0" width="100%" height="100%" fill="url(#ghana-gradient)" clip-path="url(#map-paths)" />
          @foreach ($rootRegions as $region)
            @if ($region->map_path)
                <use xlink:href="#{{$region->id}}"
                  ng-class="{'active': activeRegion=={{$region->id}}, 'hover-region': hoverRegion=={{$region->id}} }"
                  ng-click="getSubRegions({{$region->id}})"
                  ng-mouseover="hoverRegionAction({{$region->id}})"
                  ng-mouseleave="leaveRegionAction()"/>
                <text x="{{$region->map_title_x}}"
                      y="{{$region->map_title_y}}"
                      fill="white"
                      font-size="35"
                      ng-class="{'active': activeRegion=={{$region->id}}, 'hover-region': hoverRegion=={{$region->id}} }"
                      ng-click="getSubRegions({{$region->id}})"
                      ng-mouseover="hoverRegionAction({{$region->id}})"
                      ng-mouseleave="leaveRegionAction()">
                      {{$region->name}}
                </text>
            @endif
          @endforeach
        </svg>
      </div>
      <div class="col-xs-12  col-sm-12 col-md-4">
        <div class="regions-block">
          <h2 class="title">
            <img src="/images/icon/map-pin.png" alt="" ng-click="getSubRegions(0)">
            <a ng-href="<% activeRegion==rootRegionID? '#':'region/'+activeRegion%>"
              ng-class="{'root-region': activeRegion==rootRegionID}" >
              <% regionTitle %>
            </a>
          </h2>
          <div class="region-list">
            <ul ng-class="{'root-region-list':  activeRegion==rootRegionID}">
              <li ng-repeat="subRegion in subRegions"
                  ng-mouseover="hoverRegionAction(subRegion.id)"
                  ng-mouseleave="leaveRegionAction()"
              >
                <a ng-class="{'hover-region': hoverRegion==subRegion.id }"
                   ng-click="getSubRegions(subRegion.id)"
                   ng-href="<% activeRegion==rootRegionID? '#':subRegion.path%>">
                   <% $index+1 | decimalLeadingZero %>. <%subRegion.name %>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
