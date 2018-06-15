var RegionApp = angular.module('RegionApp', []);

RegionApp.config(function($interpolateProvider, $httpProvider, $logProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $logProvider.debugEnabled(false);
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

RegionApp.factory('RegionFactory', function($http) {
    return {
        getSubRegionList: function(regionId) {
          return $http.get(window.constants.ABS_URI + 'region/' + regionId + '/api');
        },
        getProjectListByRegion: function(regionId) {
          return $http.get(window.constants.ABS_URI + 'region/' + regionId + '/project/api');
        }
    };
});

RegionApp.filter('decimalLeadingZero', function(){
    return function(value){
      if(value < 10){
        return "0"+value;
      }
      return value;
    }
});

RegionApp.controller('RegionIndexCtrl', function($scope, RegionFactory, $log) {

    $scope.getSubRegions = function(regionId){
      if(regionId != $scope.activeRegion && isRootRegions(regionId)){
        RegionFactory.getSubRegionList(regionId).then(function(response){
          if(response.data.subregions){
            $scope.subRegions = response.data.subregions;
            $scope.regionTitle = response.data.name;
            $scope.activeRegion = regionId;
          }
        }).catch(function(error) {
            $log.error(error);
        });
      }else if(regionId == 0){
        $scope.activeRegion = $scope.rootRegionID;
        $scope.subRegions = window.init_data.rootRegions;
        $scope.regionTitle= window.init_data.rootRegionTitle;
      }
      return false;
    };

    $scope.hoverRegionAction = function(id){
      $scope.hoverRegion = id;
    };

    $scope.leaveRegionAction = function(){
      $scope.hoverRegion = $scope.rootRegionID;
    };

    function isRootRegions(id){
      var result = window.init_data.rootRegions.find(function(regionObj){ return regionObj.id == id});
      return (result? true:false);
    }

    (function init() {
      $scope.rootRegionID = 0;
      $scope.hoverRegion = $scope.rootRegionID;
      $scope.activeRegion = $scope.rootRegionID;
      $scope.subRegions = window.init_data.rootRegions;
      $scope.regionTitle= window.init_data.rootRegionTitle;
    })();
});

RegionApp.controller('RegionShowCtrl', function($scope, RegionFactory, $log) {

    $scope.getSubRegionList = function(regionId){
      RegionFactory.getSubRegionList(regionId).then(function(response){
        if(response.data.subregions){
          $scope.subRegions = response.data.subregions;
          $scope.subregionLabel = response.data.subregion_label;
          $scope.regionTitle = response.data.name;
        }
      }).catch(function(error) {
          $log.error(error);
      });
    };

    $scope.getProjectList = function(regionId){
      RegionFactory.getProjectListByRegion(regionId).then(function(response){
        $scope.projects = response.data;
      }).catch(function(error) {
          $log.error(error);
      });
    };


    (function init() {
      $scope.regionId = window.init_data.regionId;
      $scope.subRegions = [];
      $scope.projects = [];
      $scope.regionTitle= '';
      $scope.subregionLabel= '';
      $scope.getSubRegionList($scope.regionId);
      $scope.getProjectList($scope.regionId);
    })();
});

RegionApp.component('subregionList', {
    templateUrl: window.constants.ABS_URI + 'partials/region/subregion-list.html',
    bindings: {
      subregionLabel: '<',
      data: '<'
    },
    controller: function(RegionFactory) {
    }
});

RegionApp.component('projectListByRegion', {
    templateUrl: window.constants.ABS_URI + 'partials/project/project-list-by-region.html',
    bindings: {
      data: '<'
    },
    controller: function(RegionFactory) {

        this.$onInit = function() {
          this.currentPage = 1;
          this.conditions = {};
        };

        this.$onChanges = function (newObj) {
          this.numOfPage = 5;
          if(!angular.isUndefined(this.data) && this.data.length > 0) {
             this.maxPage = Math.floor((this.data.length - 1) / this.numOfPage + 1);
             this.totalText = this.data.length.toString().concat( ' ', this.containerName);
           }
        };
    }
});
