@extends('master/main')
@section('css_block')
  <style type="text/css">
    .image-item {
      text-align: center;
      vertical-align: middle;
    }
    .image-item img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .carousel-btn, .carousel-btn:focus {
      z-index: 15;
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      -webkit-transform: translateY(-50%);
      font-size: 28px;
      border-radius: 8px;
      padding: 20px;
      resize: vertical;
      -webkit-resize: vertical;
      background: rgba(0,0,0,0.4);
      color: rgba(256,256,256,0.7);
      text-align: center;
    }
    .carousel-btn:hover {
      background: rgba(0,0,0,0.7);
      color: white;
    }
    .carousel-btn.previous {
      left: 10px;
    }
    .carousel-btn.next {
      right: 10px;
    }

    .chart-carousel-btn, .chart-carousel-btn:focus {
      z-index: 15;
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      -webkit-transform: translateY(-50%);
      font-size: 28px;
      padding: 80px 20px;
      resize: vertical;
      -webkit-resize: vertical;
      color: #777;
      text-align: center;
    }
    .chart-carousel-btn:hover {
      /*color: black;*/
    }
    .chart-carousel-btn.previous {
      left: -40px;
    }
    .chart-carousel-btn.next {
      right: -40px;
    }

    .carousel-controls {
      z-index: 15;
      position: absolute;
      text-align: center;
      bottom: 20px;
      width: 100%;
      align-items: center;
      align-content: center;
      justify-content: center;
      -webkit-align-items: center;
      -webkit-align-content: center;
      -webkit-justify-content: center;
      display: flex;
      display: -webkit-flex;
    }
    .carousel-controls a {
      display: inline-block;
      margin: 0 5px;
      width: 10px;
      height: 10px;
      border-radius: 10px;
      background: rgb(200,200,200);
    }
    .carousel-controls-active {
      border: 8px solid rgb(200,200,200);
    }
    .arrow_disabled {
      opacity: 0.4;
    }
  </style>
@stop

@section('js_block')
  <script type="text/javascript">
    window.init_data = {
      indicator_charts : {!! $indicator_charts !!}
    };
  </script>
  <script src="{{ asset('js/chart.js') }}">
  </script>
@stop

@section('content')
  <div id="keyIndicators" ng-app="ChartApp" ng-controller="FeaturedDatasCtrl">
    <div class="container" id="myCarousel" style="height: 750px;">
        <div class="col-md-12" style="height:700px; position: relative;" ng-mouseover="showBtn = true" ng-mouseleave="showBtn = false">
        <a href="" class="chart-carousel-btn previous" ng-show="showBtn" ng-click="previousChart()"><span class="glyphicon glyphicon-chevron-left"></span></a>

        <ul style="height: 100%"
            class="carousel-inner"
            rn-carousel
            rn-carousel-buffered
            rn-carousel-index="chartIndex">
          <div class="col-md-12" style="margin-top:275px">
            <div class="alert alert-warning" ng-if="charts.length == 0">
              No data to display.
            </div>
          </div>

          <li ng-repeat="chart in charts track by $index" class="col-md-9 col-xs-12" class="image-item" style="display: inline-block;">
            <div class="col-md-12 slider-desc">
              <h6>
                <% indicator_charts[chartIndex].title %>
                @if(argo_is_accessible(Config::get('argodf.admin_function_priority')))
                  <div class="pull-right">
                    <a class="btn btn-default" ng-click="deleteChart(indicator_charts[chartIndex].id, chartIndex)" ng-disabled="deleteChartDisabled">Delete Chart</a>
                  </div>
                @endif
              </h6>
            </div>
            <highchart config="chart" ng-if="chart.series.length > 0 && chart.series[0].data.length > 0"></highchart>
            <div class="col-md-12" style="margin-top:215px" ng-if="chart.series.length == 0 || chart.series[0].data.length == 0">
              <div class="alert alert-warning">
                {{ Lang::get('form.data_analysis.component.need_login') }}
              </div>
            </div>
          </li>
        </ul>

        <a href="" class="chart-carousel-btn next" ng-show="showBtn" ng-click="nextChart()"><span class="glyphicon glyphicon-chevron-right"></span></a>

        <div class="carousel-controls" style="bottom: -10px">
          <a href="" ng-click="chartTo($index)" ng-repeat="chart in charts track by $index" ng-class="{'carousel-controls-active': chartIndex == $index}"></a>
        </div>
      </div>
    </div>
  </div>
@stop