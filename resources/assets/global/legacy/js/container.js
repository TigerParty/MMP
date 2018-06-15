var ContainerApp = angular.module('ContainerApp', ['Helper'])
.config(function($interpolateProvider, $logProvider, $httpProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $logProvider.debugEnabled(false);
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

ContainerApp.factory('ContainerFactory', function ($http){
  return{
    getContainer : function() {
        return $http.get(window.constants.ABS_URI + 'project/' + window.init_data.project_id + '/container/' +  window.init_data.container_id + '/api/');
    },
    filterContainer : function(conditions, order) {
        return $http.post(window.constants.ABS_URI + 'project/' + window.init_data.project_id + '/container/' +  window.init_data.container_id + '/api',{
          conditions: conditions,
          order: order,
          _token: window.constants.CSRF_TOKEN
        });
    }
  };
});

ContainerApp.controller('ShowCtrl', function($scope, ContainerFactory, HelperFactory) {
    (function init() {
        $scope.go_back_url = window.constants.ABS_URI + 'project/' + init_data.project_id;
        ContainerFactory.getContainer().then(function(response) {
            $scope.basic_info = response.data.basic_info;
            $scope.basic_info.created_at = HelperFactory.date_converter($scope.basic_info.created_at);
            $scope.basic_info.updated_at = HelperFactory.date_converter($scope.basic_info.updated_at);
            $scope.subcontainers = response.data.subcontainers;
            $scope.subprojects = response.data.subprojects;
            $scope.filters = response.data.filters;
            $scope.container_name = response.data.container_name;
        }).catch(function(error) {
            console.log(error);
        });
    })();
});

ContainerApp.component('basicInfo', {
    templateUrl: window.constants.ABS_URI + 'partials/project/basic-info.html',
    bindings: { data: '<' }
});

ContainerApp.component('containerList', {
    templateUrl: window.constants.ABS_URI + 'partials/project/container-list.html',
    bindings: {
      data: '<'
    }
});

ContainerApp.component('subprojectList', {
    templateUrl: window.constants.ABS_URI + 'partials/project/sub-project-list.html',
    bindings: {
      data: '<',
      filters: '<',
      containerName: '<'},
    controller: function(ContainerFactory) {


        this.filterChanged = function(filter_type, filter){
            if(!this.conditions[filter_type]) {
                this.conditions[filter_type] = {};
            }

            if(filter.value) {
                this.conditions[filter_type][filter.id] = {
                    id: filter.id,
                    value: filter.value,
                    filter_key: filter.filter_key
                };
            }else{
                delete this.conditions[filter_type][filter.id];
            }

            this.filterContainer();
        };

        this.filterContainer = function () {
            var self = this;
            ContainerFactory.filterContainer(this.conditions, this.order).then(function(response) {
                self.data = response.data;
                self.currentPage = 1;
                self.totalText = self.data.length.toString().concat( ' ', self.containerName);
                self.maxPage = Math.floor((self.data.length - 1) / self.numOfPage + 1);
            }).catch(function(error) {
                console.log(error);
            });
        }

        this.$onInit = function() {
          this.currentPage = 1;
          this.conditions = {};
          this.order = "title";
        };

        this.$onChanges = function (newObj) {
          this.numOfPage = 10;
          if(!angular.isUndefined(this.data) && this.data.length > 0) {
             this.maxPage = Math.floor((this.data.length - 1) / this.numOfPage + 1);
             this.totalText = this.data.length.toString().concat( ' ', this.containerName);
           }
        };
    }
});
