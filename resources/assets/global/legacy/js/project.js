var ProjectApp = angular.module('ProjectApp', ['ngMaterial', 'ui.bootstrap', 'ngAnimate', 'leaflet-directive', 'AttachUploader', 'DatePicker', 'Helper', 'angular-carousel', 'highcharts-ng','ArgoMap'])
.config(function($interpolateProvider, $logProvider, $httpProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $logProvider.debugEnabled(false);
    $httpProvider.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
})
.constant("CGY_NAMESPACE", 'project');

ProjectApp.factory('ProjectFactory', function ($http) {
    return {
        getNextReport : function(p_id, origin_amount) {
            return $http.post(window.constants.ABS_URI + 'webapi/get_next_report', {
                project_id : p_id,
                origin_amount : origin_amount,
                _token : window.constants.CSRF_TOKEN
            });
        },
        sendFBCommentNotification : function(comment_info) {
            return $http.post(window.constants.ABS_URI + 'webapi/send_fb_comment_notification', {
                comment_info : comment_info,
                _token : window.constants.CSRF_TOKEN
            });
        },
        getProject : function(project_id) {
            return $http.get(window.constants.ABS_URI + 'project/' + project_id + '/api/');
        },
        getFormData : function(project_id, form_id) {
            return $http.get(window.constants.ABS_URI + 'project/' + project_id + '/form/' + form_id);
        },
        getAggregationData : function(project_id) {
            return $http.get(window.constants.ABS_URI + 'project/' + project_id + '/aggregation/api');
        },
        getIndicatorData : function(project_id, indicator_id) {
            return $http.get(window.constants.ABS_URI + 'project/' + project_id +'/indicator/'+ indicator_id +'/api');
        }
    };
});

ProjectApp.filter('removeHtmlTag', function() {
    return function(string){
        if(!angular.isString(string)){
            return string;
        }
        return string.replace(/<(?:.|\n)*?>/gm, '');
    };
});

ProjectApp.filter('show', function() {
  return function (reports,idx) {
      if(!angular.isUndefined(reports)){
        var reports_count = reports.length;
        var show_items = 5;
        if(reports_count <= show_items){
          return reports;
        }else
        {
          var last_idx = reports_count-show_items;
          if(idx <= last_idx)
          {
             return reports.slice(idx, idx+show_items);
          }else{
             return reports.slice(last_idx, reports_count-1);
          }
        }
      }
  };
});

ProjectApp.controller('ShowCtrl', function($window, $rootScope, $scope, $uibModal, $log, $interval, HelperFactory, ProjectFactory) {
    $scope.showForm = function(form_id){
      $scope.submenu_pointer = form_id;
      if(form_id !== null){
        ProjectFactory.getFormData($scope.project_id, form_id).then(function(response){
           $scope.form = response.data.form;
        }).catch(function(error) {
            console.log(error);
        });
      }else {
        $scope.form = $scope.basic_form;
      }
    };

    $scope.showAggregatedData = function() {
        $scope.submenu_pointer = 'aggregated_data';
        $scope.form = null;

        if($scope.aggregated_data == undefined)
        {
            ProjectFactory.getAggregationData($scope.project_id).then(function(response) {
                $scope.aggregated_data = response.data.aggregated_fields;
            }).catch(function(error) {
                console.log(error);
            });
        }
    };

    $scope.showComment = function() {
        $scope.submenu_pointer = 'comment';
        $scope.form = null;
    };

    (function init() {
        $scope.submenu_pointer = null;
        $scope.project_id = window.init_data.project_id;
        $scope.container_id = window.init_data.container_id;
        ProjectFactory.getProject($scope.project_id).then(function(response){
            $scope.basic_info = response.data.basic_info;
            $scope.map = response.data.map;
            $scope.project_title = response.data.title;
            $scope.status_info = response.data.status_info;
            $scope.status_info.created_at = HelperFactory.date_converter($scope.status_info.created_at);
            $scope.status_info.updated_at = HelperFactory.date_converter($scope.status_info.updated_at);
            $scope.container_id = response.data.container_id;
            $scope.container_name = response.data.container_name;
            $scope.form = response.data.form;
            $scope.basic_form = response.data.form;
            $scope.forms = response.data.forms;
            $scope.regions = response.data.regions;
            $scope.project_charts = response.data.charts; //TODO: Cleanr legacy code
            $scope.subcontainers = response.data.subcontainers;
            $scope.indicator_ids =  response.data.indicator_ids;
            $scope.attachments =  response.data.attachments;
        }).catch(function(error) {
            console.log(error);
        });

        $window.fbAsyncInit = function()
        {
            FB.Event.subscribe('comment.create', function(response) {
                ProjectFactory.sendFBCommentNotification(response).then(function(response) {
                    //
                });
            });
        };

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    })();
});

ProjectApp.component('basicInfo', {
   templateUrl: window.constants.ABS_URI + 'partials/project/basic-info.html',
   bindings: { data: '<' },
   controller: function($sce){
     this.$onChanges = function(){
          if(this.data){
              this.desc_trusted = $sce.trustAsHtml(this.data.descirption_html);
          }
     }
   }
});

ProjectApp.component('statusInfo', {
    templateUrl: window.constants.ABS_URI + 'partials/project/status-info.html',
    bindings: { data: '<' }
});

ProjectApp.component('reportSlider', {
  templateUrl: window.constants.ABS_URI + 'partials/project/slider.html',
  bindings: { data: '<',
              formName: '<',
              noreportString: '@' },
  controller: function ($uibModal, $q, $interval, HelperFactory, ProjectFactory ){

    this.itemSwitch = function (switchBy) {
        var self = this;
        var interval_sec = (switchBy == 'manually') ? 10000 : 5000;

        $interval.cancel(this.itemTimer);
        this.itemTimer = $interval(function() {
            var last_item_index = self.data[self.focus_report_index].items.length - 1;
            var current_item_index = self.itemIndex;

            if(current_item_index + 1 <= last_item_index){
              self.itemIndex += 1;
              self.itemSwitch('automatically');
            }else{
              self.nextReport('automatically');
            }

        }, interval_sec);

    }

    this.itemTo = function (idx){
        this.itemIndex = idx;
        this.itemSwitch('manually');
    };

    this.nextItem = function () {
        var last_item_index = this.data[this.focus_report_index].items.length - 1;
        var current_item_index = this.itemIndex;

        if(current_item_index + 1 <= last_item_index)
        {
            this.itemIndex += 1;
            this.itemSwitch('manually');
        }
        else
        {
            this.nextReport('manually');
        }
    };

    this.previousItem = function () {
        var first_item_index = 0;
        var current_item_index = this.itemIndex;

        if(current_item_index - 1 >= first_item_index)
        {
            this.itemIndex -= 1;
            this.itemSwitch('manually');
        }
        else
        {
            this.previousReport('manually');
        }
    };

    this.reportTo = function (idx) {
        this.focus_report_index = idx;
        this.itemIndex = 0;
        this.itemSwitch('manually');
    }

    this.nextReport = function (switchBy) {
        var last_report_index = this.data.length - 1;
        var current_report_index = this.focus_report_index;

        this.itemIndex = 0;
        if(current_report_index + 1 <= last_report_index){
            this.focus_report_index += 1;
        }else{
            this.focus_report_index = 0;
        }
        this.itemSwitch(switchBy);
    };

    this.previousReport = function (switchBy) {
        var first_report_index = 0;
        var current_report_index = this.focus_report_index;
        var last_report_index = this.data.length - 1 ;

        this.itemIndex = 0;
        if(current_report_index - 1 >= first_report_index){
            this.focus_report_index -= 1;
        }else{
            this.focus_report_index = last_report_index;
        }
        this.itemSwitch(switchBy);
    };

    this.enlargeCtrl = function () {
        $interval.cancel(this.itemTimer);
        var self = this;
        $uibModal.open({
          animation: true,
          templateUrl: window.constants.ABS_URI + 'partials/img-slide-bubble.html',
          controller: 'ImageSlidePopCtrl',
          size: 'lg',
          resolve:{
            images: function () {
                return self.data[self.focus_report_index].items;
            },
            imageIndex: function () {
                return self.itemIndex;
            }
          }
        }).result.then(function() {
            self.itemSwitch();
        }, function() {
            self.itemSwitch();
        });

    }

    this.$onInit = function () {
      this.focus_report_index = 0;
    };

    this.$onChanges = function (newObj) {
      if(!angular.isUndefined(this.data)) {
        $interval.cancel(this.itemTimer);
        if (this.data.length > 0)
        {
          this.focus_report_index = 0;
          this.itemIndex = 0;
          this.report_amount = this.data.length;
          this.itemSwitch('automatically');
        }

      }
    };
 }
});

ProjectApp.component('aggregatedData', {
    templateUrl: window.constants.ABS_URI + 'partials/project/aggregated-data.html',
    bindings: {
        data: '<',
    },
    controller: function($log) {
        var scope = this;
        scope.isObject = angular.isObject;

        this.$onChanges = function() {
            angular.forEach(scope.data, function(data, index) {
                if(angular.isObject(data.value)) {
                    data.chart = {
                        options: {
                            colors : ['#4C86FA', '#5DA457', "#EF6B00", "#8637E5", "#865413", "#A1A3A5", "#FFEB3B", "#ff0cf5", "#53fff1", "#7bff53"],
                            chart: {
                                backgroundColor: null,
                                type: 'pie'
                            }

                        },
                        title: {
                            text: data.title,
                        },
                        size: {
                            "height": "240"
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

                    angular.forEach(data.value, function(value) {
                        if(value.count > 0)
                        {
                            data.chart.series[0].data.push({
                                name: value.value == 'Others' ? '?' : value.value,
                                x: parseInt(value.count),
                                y: parseInt(value.count),
                                dataLabels: {
                                    enabled: true,
                                    format: value.value == 'Others' ? '?' : value.value,
                                    style: {color: "black", textShadow: "none", fontSize: '14px'}
                                }
                            });
                        }
                    });
                }
            });
        }
    }
});

ProjectApp.component('formDataWrap', {
    templateUrl: window.constants.ABS_URI + 'partials/project/form-data-wrap.html',
    bindings: {
        data: '<',
        titleGpsAvgspeed: '@',
        titleGpsDistance: '@',
        titleGpsStartTime: '@',
        titleGpsEndTime: '@'
    },
    controller: function($log, $filter, HelperFactory) {
        var scope = this;

        this.formatFieldValue = function() {
            angular.forEach(this.data, function(form_field, index) {
                if(form_field.field_template_key == 'gps_tracker') {
                    form_field.value = JSON.parse(form_field.value);
                    var start_timestamp = new Date(form_field.value.start_at).getTime()/1000;
                    var end_timestamp = new Date(form_field.value.end_at).getTime()/1000;
                    form_field.value.tracker_distance = Math.round((end_timestamp - start_timestamp)/3600 * form_field.value.avg_speed *100)/100;
                    form_field.value.start_at = HelperFactory.datetime_converter(form_field.value.start_at);
                    form_field.value.end_at = HelperFactory.datetime_converter(form_field.value.end_at);

                } else if (form_field.field_template_key == 'check_box_group') {
                    try
                    {
                        form_field.value = JSON.parse(form_field.value).join(", ");
                    }
                    catch(err)
                    {
                        form_field.value = null;
                    }

                } else if (form_field.field_template_key == 'iri_tracker') {
                    form_field.value = "(IRI tracker data)"

                } else if (form_field.field_template_key == 'date') {
                    form_field.value = $filter('date')(form_field.value, 'dd LLLL, yyyy');
                }

                if (!form_field.show_if) {
                    form_field.show = true;
                } else {
                    // Default hide the field if show_if condition exists
                    form_field.show = false;

                    try {
                        var showIfObj = form_field.show_if;
                        scope.data.forEach(function(sourceField){
                            if (showIfObj[sourceField.form_field_id] != undefined &&
                                showIfObj[sourceField.form_field_id] == sourceField.value
                            ){
                                $log.debug("Mark field "+form_field.form_field_id+" as hidden by showIf condition.");
                                scope.data[index].show = true;
                                return;
                            }
                        });
                    } catch (err) {
                        $log.error("Illegal show_if value in form field "+form_field.id);
                    }
                }
            });
        };

        this.$onChanges = function (newObj) {
            if(!angular.isUndefined(this.data) && this.data.length > 0) {
                this.formatFieldValue();
            }
        };
    }
});

ProjectApp.component('projectIndicator', {
    templateUrl: window.constants.ABS_URI + 'partials/project/indicator.html',
    bindings: {
        projectId: '<',
        indicatorIds: '<',
        highchartObjects: '=',
    },
    controller: function($interval, ProjectFactory) {
        this.setChartTimer = function(time_delay) {
            var self = this;
        };

        this.previousChart = function() {
            if(this.chartIndex == 0) {
                this.chartIndex = this.highchartObjects.length - 1;
            }else{
                this.chartIndex -= 1;
            }
            this.resetChartTimer();
        };

        this.nextChart = function() {
            if(this.chartIndex+1 == this.highchartObjects.length) {
                this.chartIndex = 0;
            }else{
                this.chartIndex += 1;
            }
            this.resetChartTimer();
        };

        this.chartTo = function (idx) {
            this.chartIndex = idx;
            this.resetChartTimer();
        };

        this.resetChartTimer = function() {
            $interval.cancel(this.chartTimer);
            this.setChartTimer(10000);
        };

        this.getIndicatorData = function(indicatorId) {
            var self = this;
            ProjectFactory.getIndicatorData(this.projectId, indicatorId).then(function(response) {
                self.highchartObjects.push(response.data);
            }).catch(function(response) {
                $interval.cancel(self.queryBuffer);
            });
        };

        this.$onInit = function() {
            this.chartIndex = 0;
            this.setChartTimer(10000);
        };

        this.$onChanges = function () {
            var self = this;

            this.highchartObjects = [];
            if(this.indicatorIds && this.indicatorIds.length > 0) {
                self.queryBuffer = $interval(function() {
                    if(self.highchartObjects.length < self.indicatorIds.length) {
                        self.getIndicatorData(self.indicatorIds[self.highchartObjects.length]);
                    } else {
                        $interval.cancel(self.queryBuffer);
                    }
                }, 1500);
            }
        };
    }
});

ProjectApp.component('containerList', {
    templateUrl: window.constants.ABS_URI + 'partials/project/container-list.html',
    bindings: {
      data: '<'
    }
});

ProjectApp.component('subprojectList', {
    templateUrl: window.constants.ABS_URI + 'partials/project/sub-project-list.html'
});

ProjectApp.component('projectCategory', {
    templateUrl: window.constants.ABS_URI + 'partials/project/category.html',
    bindings: { title: '@',
                data: '<'}
});

ProjectApp.component('projectAttachment', {
    templateUrl: window.constants.ABS_URI + 'partials/project/attachment.html',
    bindings: { title: '@',
                data: '<'
              }
});

ProjectApp.controller('ImageSlidePopCtrl', function ($rootScope, $scope, $uibModalInstance, images, imageIndex) {
    $scope.images = images;
    $scope.imageIndex = imageIndex;

    function playVideo() {

    };

    $scope.previousImage = function() {
        if($scope.imageIndex > 0)
        {
            $scope.imageIndex -= 1;
            if($scope.images[$scope.imageIndex].attachment_type.includes('video')){
                playVideo();
            }
        }
    };

    $scope.nextImage = function() {
        if($scope.imageIndex < $scope.images.length - 1)
        {
            $scope.imageIndex += 1;
        }
    };

    $scope.imageTo = function(idx) {
        $scope.imageIndex = idx;
    };

    $scope.close = function () {

        $uibModalInstance.close();
    };
});
