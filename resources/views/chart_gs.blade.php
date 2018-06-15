@extends('master/main')

@section('css_block')
@stop

@section('js_block')


    <script src="{{ asset('js/standalone-framework.js') }}"></script>
    {{-- <script src="{{ asset('js/highcharts.js') }}"></script> --}}
    <script src="{{ asset('js/highcharts_style.js') }}"></script>
    <script src="{{ asset('js/key_indicator.js') }}"></script>

    <script>

        window.init_data = {
            slides:
            [
                {
                    title:'Key Indicators',
                    sub_title1:'Performance Statistics for May/June Wassce for the Core Subject',
                    sub_title2:'GRADE A1-C6',
                    highchartsOpts:[{
                        chart: {
                            type: 'line',
                            backgroundColor: 'rgba(0,0,0,0)'
                        },
                        title: {
                            text: null
                        },
                        credits: {
                          enabled: false
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: {
                            categories: ['2006', '2007', '2008', '2009']
                        },
                        yAxis: {
                            title: {
                                text: 'WASSCE Performance (%)'
                            },
                            plotLines: [{
                                value: 1,
                                width: 1,
                                color: '#808080'
                            }]
                        },
                        tooltip: {
                            headerFormat: '',
                            valueSuffix: null
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom',
                            borderWidth: 0
                        },
                        series: [
                            {
                                name: 'English Language',
                                data: [32.17, 27.72, 49.18, 43.56]
                            }, {
                                name: 'Integrated Science',
                                data: [24.55, 23.62, 26.5, 34.26]
                            }, {
                                name: 'Mathematics (core)',
                                data: [31.14, 25.1, 26.02, 28.43]
                            }, {
                                name: 'Social Studies',
                                data: [67.04, 75.21, 60.11, 76.58]
                            }
                        ]
                    }]
                },
                {
                    title:'Key Indicators',
                    sub_title1:'Performance Statistics for May/June Wassce for the Core Subject',
                    sub_title2:'GRADE A1-C6',
                    highchartsOpts:[{
                        chart: {
                            type: 'line',
                            backgroundColor: 'rgba(0,0,0,0)'

                        },
                        title: {
                            text: null
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories: ['2010', '2012', '2013', '2014', '2015']
                        },
                        yAxis: {
                            title: {
                                text: 'WASSCE Performance (%)'
                            },
                            plotLines: [{
                                value: 1,
                                width: 0,
                                color: '#808080'
                            }]
                        },
                        tooltip: {
                            headerFormat: '',
                            valueSuffix: null
                        },
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom',
                            borderWidth: 0
                        },
                        series: [{
                            name: 'English Language',
                            data: [75.9, 66.9, 65.7, 45.2, 50.29]
                        }, {
                            name: 'Integrated Science',
                            data: [42, 55.3, 28.7, 23.63, 9.1]
                        }, {
                            name: 'Mathematics (core)',
                            data: [43.8, 49.4, 36.6, 32.4, 25.04]
                        }, {
                            name: 'Social Studies',
                            data: [82.2, 87.1, 81.4, 54.7, 51.84]
                        }]
                    }]
                },
                {
                    title:'Key Indicators',
                    sub_title1:'Performance Statistics for May/June Wassce for the Elective Subject',
                    sub_title2:'GRADE A1-C6',
                    highchartsOpts:[{
                        chart: {
                            type: 'line',
                            backgroundColor: 'rgba(0,0,0,0)'
                        },
                        title: {
                            text: null
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories: ['2006', '2007', '2008', '2009']
                        },
                        yAxis: {
                            title: {
                                text: 'WASSCE Performance (%)'
                            },
                            plotLines: [{
                                value: 1,
                                width: 0,
                                color: '#808080'
                            }]
                        },
                        tooltip: {
                            valueSuffix: null
                        },
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom',
                            borderWidth: 0
                        },
                        series: [{
                            name: 'Mathematics (elective)',
                            data: [50.36, 35.15, 34.44, 34.69]
                        }, {
                            name: 'Biology',
                            data: [65.07, 55.06, 62.51, 53.62]
                        }, {
                            name: 'Chemistry',
                            data: [42.24, 27.82, 24.79, 29.06]
                        }, {
                            name: 'Physics',
                            data: [48.83, 42.62, 56.24, 43.67]
                        }]
                    }]
                },
                {
                    title:'Key Indicators',
                    sub_title1:'Performance Statistics for May/June Wassce for the Elective Subject',
                    sub_title2:'GRADE A1-C6',
                    highchartsOpts:[{
                        chart: {
                            type: 'line',
                            backgroundColor: 'rgba(0,0,0,0)'
                        },
                        title: {
                            text: null
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories: ['2009', '2010', '2011', '2014', '2015']
                        },
                        yAxis: {
                            title: {
                                text: 'WASSCE Performance (%)'
                            },
                            plotLines: [{
                                value: 1,
                                width: 0,
                                color: '#808080'
                            }]
                        },
                        tooltip: {
                            headerFormat: '',
                            valueSuffix: null
                        },
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom',
                            borderWidth: 0
                        },
                        series: [{
                            name: 'Mathematics (elective)',
                            data: [66.7, 75.1, 46.8, 20.3, 24.34]
                        }, {
                            name: 'Biology',
                            data: [73.6, 59, 62, 59.6, 49.43]
                        }, {
                            name: 'Chemistry',
                            data: [51.6, 58.9, 52.9, 50, 49.46]
                        }, {
                            name: 'Physics',
                            data: [71.3, 70.6, 59.2, 52.7, 63.23]
                        }]
                    }]
                },
                {
                    title:'Key Indicators',
                    sub_title1:'Four-year (2010-2013) Comparative Trend on performance in May/June WASSCE in Member Countries',
                    sub_title2:'Grade A1-C6 in Core Subjects',
                    highchartsOpts:[{
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: {
                            categories: [
                                'Nigeria',
                                'Ghana',
                                'Sierra Leone',
                                'The Gambia',
                                'Liberia'
                            ],
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'English Language (%)'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:16px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [{
                                name: '2010',
                                data: [35.13, null, 13.72, 8.69, null]
                            }, {
                                name: '2011',
                                data: [57.24, 75.9, 11.91, 14.9, null]
                            }, {
                                name: '2012',
                                data: [58.21, 66.9, null, 16.95, null]
                            }, {
                                name: '2013',
                                data: [51.62, 65.7, null,16.23, 3.75]
                            }
                        ]
                    }]
                },
                {
                    title:'Key Indicators',
                    sub_title1:'Four-year (2010-2013) Comparative Trend on performance in May/June WASSCE in Member Countries',
                    sub_title2:'Grade A1-C6 in Core Subjects',
                    highchartsOpts:[{
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: {
                            categories: [
                                'Nigeria',
                                'Ghana',
                                'Sierra Leone',
                                'The Gambia',
                                'Liberia'
                            ],
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Science (%)'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:16px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [
                            {
                                name: '2010',
                                data: [null, null, 37.26, 13.67, null]
                            }, {
                                name: '2011',
                                data: [null, 42, 28.97, 13.06, null]
                            }, {
                                name: '2012',
                                data: [null, 55.3, null, 19.11, null]
                            }, {
                                name: '2013',
                                data: [null, 28.7, null, 6.75, null]
                            }
                        ]
                    }]
                },
                {
                    title:'Key Indicators',
                    sub_title1:'Four-year (2010-2013) Comparative Trend on performance in May/June WASSCE in Member Countries',
                    sub_title2:'Grade A1-C6 in Core Subjects',
                    highchartsOpts:[{
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: {
                            categories: [
                                'Nigeria',
                                'Ghana',
                                'Sierra Leone',
                                'The Gambia',
                                'Liberia'
                            ],
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Math(core)(%)'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:16px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [{
                            name: '2010',
                            data: [41.95, null, 5.15, 3.65, null]
                        }, {
                            name: '2011',
                            data: [40.35, 43.8, 3.01, 5.29, null]
                        }, {
                            name: '2012',
                            data: [50.58, 49.4, null, 6.75, null]
                        }, {
                            name: '2013',
                            data: [54.18, 36.6, null, 10.45, 0.07]
                        }]
                    }]
                },
                {
                    title:'Key Indicators',
                    sub_title1:'Four-year (2010-2013) Comparative Trend on performance in May/June WASSCE in Member Countries',
                    sub_title2:'Grade A1-C6 in Core Subjects',
                    highchartsOpts:[{
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: {
                            categories: [
                                'Nigeria',
                                'Ghana',
                                'Sierra Leone',
                                'The Gambia',
                                'Liberia'
                            ],
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Maths Elect. (%)'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:16px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [
                        {
                            name: '2010',
                            data: [36.88, null, 7.06, 56.12, null]
                        }, {
                            name: '2011',
                            data: [62.91, 66.7, 13.83, 67.16, null]
                        }, {
                            name: '2012',
                            data: [51.92, 75.1, null, 39.92, null]
                        }, {
                            name: '2013',
                            data: [29.52, 46.8, null,28.63, null]
                        }]
                    }]
                },
                {
                    title:'Key Indicators',
                    sub_title1:'Four-year (2010-2013) Comparative Trend on performance in May/June WASSCE in Member Countries',
                    sub_title2:'Grade A1-C6 in Core Subjects',
                    highchartsOpts:[{
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: {
                            categories: [
                                'Nigeria',
                                'Ghana',
                                'Sierra Leone',
                                'The Gambia',
                                'Liberia'
                            ],
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Biology (%)'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:16px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [{
                            name: '2010',
                            data: [49.65, null, 5.94, 20.32, null]
                        }, {
                            name: '2011',
                            data: [38.5, 73.6, 2.86, 11.63, null]
                        }, {
                            name: '2012',
                            data: [35.66, 59, null, 21.06, null]
                        }, {
                            name: '2013',
                            data: [51.66, 62, null, 30.82, 0]
                        }]
                    }]
                },
                {
                    title:'Key Indicators',
                    sub_title1:'Four-year (2010-2013) Comparative Trend on performance in May/June WASSCE in Member Countries',
                    sub_title2:'Grade A1-C6 in Core Subjects',
                    highchartsOpts:[{
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: {
                            categories: [
                                'Nigeria',
                                'Ghana',
                                'Sierra Leone',
                                'The Gambia',
                                'Liberia'
                            ],
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Chemistry (%)'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:16px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [{
                            name: '2010',
                            data: [50.7, null, 3.86, 24.2, null]
                        }, {
                            name: '2011',
                            data: [49.54, 51.6, 6.23, 26.39, null]
                        }, {
                            name: '2012',
                            data: [43.13, 58.9, null, 27.57, null]
                        }, {
                            name: '2013',
                            data: [72.04, 52.9, null,35.2, 0]
                        }]
                    }]
                },
                {
                    title:'Key Indicators',
                    sub_title1:'Four-year (2010-2013) Comparative Trend on performance in May/June WASSCE in Member Countries',
                    sub_title2:'Grade A1-C6 in Core Subjects',
                    highchartsOpts:[{
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: {
                            categories: [
                                'Nigeria',
                                'Ghana',
                                'Sierra Leone',
                                'The Gambia',
                                'Liberia'
                            ],
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Physics (%)'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:16px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [{
                            name: '2010',
                            data: [51.27, null, 4.05, 33.75, null]
                        }, {
                            name: '2011',
                            data: [63.94, 71.3, 11.39, 47.14, null]
                        }, {
                            name: '2012',
                            data: [68.74, 70.6, null, 47.38, null]
                        }, {
                            name: '2013',
                            data: [46.62, 59.1, null, 27.28, 0]
                        }]
                    }]
                },
            ]
        };
    </script>
@stop

@section('content')

<div id="keyIndicators" ng-app="Key_IndicatorApp" ng-controller="CarouselCtrl">
    <div id="myCarousel" class="carousel slider">
        <!-- Indicators -->
        <div uib-carousel active="slide.active" interval="intervalTime" no-wrap="noWrapSlides">

          <div uib-slide ng-repeat="slide in slides track by $index" index="$index">
              <div class="container">
                <div class="col-md-12 slider-desc">
                  <p><% slide.title %></p>
                  <h6><% slide.sub_title1 %></h6>
                  <h6 class="subhead"><% slide.sub_title2 %></h6>
                </div>
                <div class="col-md-12 slider-graph">
                    <div class="graph-canvas" 
                        ng-repeat="highchartsOpt in slide.highchartsOpts" 
                        highcharts="highchartsOpt">
                    </div>
                </div>
                <img src="images/bkg_graphSection.png" 
                    alt="" 
                    class="bkg-building col-md-12"
                >
              </div>

              <div class="carousel-caption">
              </div>
          </div>

        </div>
    </div>
</div><!-- #keyIndicators -->
@stop