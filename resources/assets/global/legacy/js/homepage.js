var HomepageApp = angular.module('HomepageApp', ['Helper', 'highcharts-ng']);

HomepageApp.config(function($interpolateProvider, $logProvider, $httpProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $httpProvider.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
});

HomepageApp.factory('HomepageFactory', function ($http) {
    return {
        getRecentProgress: function() {
            return $http.get(window.constants.ABS_URI + 'home/api');
        },
        sendFBCommentNotification : function(comment_info) {
            return $http.post(window.constants.ABS_URI + 'webapi/send_fb_comment_notification', {
                comment_info : comment_info,
                _token : window.constants.CSRF_TOKEN
            });
        }
    };
});


HomepageApp.controller('HomepageCtrl', function($window, HomepageFactory, HelperFactory) {
    var scope = this;
    (function init() {
        scope.recentProjects = [];
        scope.projectStatus = [];

        scope.chartConfig = {
            backgroundColor: null ,
            options: {
                colors : ['#4C86FA', '#5DA457', "#EF6B00", "#8637E5", "#865413", "#A1A3A5", "#FFEB3B", "#ff0cf5", "#53fff1", "#7bff53", "#fc0b0b"],
                chart: {
                    backgroundColor: null,
                    type: 'pie'
                }

            },
            title: {
                text: '',
            },
            size: {
                "height": "360"
            },
            series: [{
                size: '100%',
                innerSize: '50%',
                borderWidth: 0,
                tooltip: {
                    pointFormat: '<b>{point.x}</b><br/>',
                },
                data: []
            }]
        };

        HomepageFactory.getRecentProgress().then(function(response) {
            scope.projectStatus = response.data.status;
            scope.totalValue = response.data.project_count;
            var otherProjectsCount = scope.totalValue;
            scope.projectStatus.map(function(status) {
                otherProjectsCount -= status.total;
            });
            scope.projectStatus.push({
                name: 'Others',
                total: otherProjectsCount
            });
            angular.forEach(scope.projectStatus, function(status, key) {
                status["percentage"] = scope.totalValue > 0 ?
                                    parseFloat((parseInt(status.total)/scope.totalValue)*100).toFixed(2) : 0;
                if(status.total > 0){
                    this.series[0].data.push({
                        name: status.name,
                        x: parseInt(status.total),
                        y: parseInt(status.total),
                        dataLabels: {
                            enabled: false,
                            format: '{y}%',
                            style: {color: "white", textShadow: "none", fontSize: '14px'}
                        }
                    });
                }
            }, scope.chartConfig);


            scope.recentProjects = response.data.projects;
            angular.forEach(scope.recentProjects, function(project, key) {
              if(project.project_status === null){ project.project_status = "-";}
              project["projectURL"] = window.constants.ABS_URI + 'project/' + project.id;
              project["coverImageURL"] = window.constants.ABS_URI + 'file/' + project.cover_image_id;
            });
        })
        .catch(function(response) {
            console.log('Home Api got an error.');
        });

        $window.fbAsyncInit = function()
        {
            FB.Event.subscribe('comment.create', function(response) {
                HomepageFactory.sendFBCommentNotification(response).then(function(response) {
                    //
                });
            });
        };

    })();
});


