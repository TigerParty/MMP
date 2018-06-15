var ExploreApp = angular.module("ExploreApp", ['ui.bootstrap', 'leaflet-directive', 'Helper']);

ExploreApp.config(function($interpolateProvider, $httpProvider, $logProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $logProvider.debugEnabled(false);
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

ExploreApp.filter('titleize', function() {
    return function(input) {
        input = input || '';
        return input.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    };
});

ExploreApp.filter('removeHtmlTag', function(){
    return function(string){
        if(!angular.isString(string)){
            return string;
        }
        return string.replace(/<(?:.|\n)*?>/gm, '');
    };
});

ExploreApp.filter('startFrom', function() {
    return function(input, start) {
      if(input)
      {
        start = +start; //parse to int
        return input.slice(start);
      }
    };
});

ExploreApp.factory('ExploreFactory', function($http) {
    return {
        getContainer: function(containerId) {
            return $http.get(window.constants.ABS_URI + 'container/' + containerId + '/api');
        },
        getRootRegions: function() {
            return $http.get(window.constants.ABS_URI + 'region/api');
        },
        getRegions: function(regionId) {
            return $http.get(window.constants.ABS_URI + 'region/' + regionId + '/api');
        },
        searchProjects: function(conditions, orderBy, fields) {
            return $http.post('/explore/queryApi', {
                conditions: conditions,
                order_by: orderBy,
                fields: fields,
                _token: window.constants.CSRF_TOKEN
            });
        },
        getChildRegions : function(id) {
            return $http.post(window.constants.ABS_URI + 'webapi/districts_by_region', {
                region_id : id,
                _token : window.constants.CSRF_TOKEN
            });
        },
        getReporterLocation: function(){
            return $http.post(window.constants.ABS_URI + 'map/api/reporter_location');
        },
        getCitizenReports: function(){
            return $http.post(window.constants.ABS_URI + 'map/api/citizen_report');
        },
        getTrackers: function(){
            return $http.get(window.constants.ABS_URI + 'map/api/tracker');
        }
    };
});

ExploreApp.service('MapService', function(HelperFactory, ExploreFactory, $interval) {
    var that = this;

    this.reporterUpdater = undefined;

    this.map = {
        center: window.constants.HOME_MAP_CENTER,
        defaults: {
            tileLayer: 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            zoomControlPosition: 'topright',
            tileLayerOptions: {
                opacity: 0.9,
                detectRetina: true,
                reuseTiles: true,
            },
            scrollWheelZoom: true
        },
        paths: [],
        markers: {},
        events: {
            path: {
                enable: ['click']
            }
        },
        layers: {
            baselayers: {
                osm: {
                    name: 'OpenStreetMap',
                    type: 'xyz',
                    url: 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                }
            },
            overlays: {}
        }
    };

    this.syncMapObjects = function(projects) {
        that.projects = projects;
        that.syncMarkersByProjects(projects);
    };

    this.removeMapLayerObjects = function(layer) {
        if(layer == 'project' || layer == 'reporter_location' || layer == 'citizen_report' || layer == 'tracker_attachments')
        {
            angular.forEach(that.map.markers, function(marker, index) {
                if(marker.layer == layer)
                {
                    delete that.map.markers[index];
                }
            });
        }
        else if(layer == 'tracker')
        {
            angular.forEach(that.map.paths, function(marker, index) {
                if(marker.layer == layer)
                {
                    delete that.map.paths[index];
                }
            });
        }
    };

    this.syncPathByCoordinates = function(trackers) {
        that.removeMapLayerObjects('tracker');
        trackers.map(function(tracker, index) {
            if (tracker.path) {
                tracker.path.map(function(coordinate_groups, i) {
                    var path = {
                        color: '#217c27',
                        weight: 3,
                        layer: 'tracker',
                        latlngs: [],
                        trackerIndex: index,
                        trackerId: tracker.id,
                    };

                    coordinate_groups.map(function(coordinate, j){
                        path.latlngs[j] = {
                            lat: coordinate[0],
                            lng: coordinate[1]
                        };
                    });
                    tracker.first_attachment = (tracker.attaches.length > 0) ? window.constants.ABS_URI + 'file/' + tracker.attaches[0]['pivot']['attachment_id'] : window.constants.ABS_URI + 'images/default_thumb_explore.png';

                    path.message = '<div class="map-bubble">'+
                        '<div class="row">'+
                        '<div class="col-md-6">'+
                        '<div class="map-bubble-thumbnail" >'+
                        '<div class="img" ' + 'style="background-image: url(\''+ tracker.first_attachment +'\');"' + ' alt=""></div>'+
                        '</div>'+
                        '</div>'+
                        '<div class="col-md-6 map-bubble-desc">';

                    if (tracker.title) {
                        path.message += '<p>'+ tracker.title +'</p>';
                    }

                    if (tracker.meta) {
                        path.message += '<p> Average Speed: '+ tracker.meta.avg_speed +'</p>'+
                                        '<p> Start time: ' + tracker.meta.start_at +'</p>'+
                                        '<p> End time: '+ tracker.meta.end_at +'</p>';
                    }

                    path.message += '</div></div></div>';

                that.map.paths.push(path);
                });
            }
        });
    };

    this.syncMarkersByProjects = function(projects) {
        that.removeMapLayerObjects('project');
        projects.map(function(project, index) {
            project.last_report_datetime = HelperFactory.datetime_converter(project.updated_at);
            if (project.lat && project.lng) {
                that.map.markers[project.id] = {
                    lat: parseFloat(project.lat),
                    lng: parseFloat(project.lng),
                    icon: {
                        iconUrl: window.constants.ABS_URI + 'images/icon/pin.png',
                        iconSize: [20, 20],
                        popupAnchor: [0, -10]
                    },
                    layer: 'project',
                    message: '<popup index="' + index + '" ></popup>'
                };
            }
        });
    };

    this.syncMarkersByCitizenReports = function(citizenReports) {
        that.removeMapLayerObjects('citizen_report');
        angular.forEach(citizenReports, function(report, index) {
            report.created_at_datetime = HelperFactory.datetime_converter(report.created_at);
            report.img_path = (report.attachment_id) ? window.constants.ABS_URI + 'file/' + report.attachment_id : window.constants.ABS_URI + 'images/default_thumb_explore.png';

            if (report.lat && report.lng) {
                var marker_id = 'citizenReports_' + report.id;
                this.markers[marker_id] = {
                    lat: parseFloat(report.lat),
                    lng: parseFloat(report.lng),
                    icon: {
                        iconUrl: window.constants.ABS_URI + 'images/pin_citizen.png',
                        iconSize: [20, 20],
                        popupAnchor: [0, -10]
                    },
                    layer: 'citizen_report',
                    message: '<div class="map-bubble">'+
                        '<div class="row">'+
                        '<div class="col-md-6">'+
                        '<div class="map-bubble-thumbnail" >'+
                        '<div class="img" style="background-image: url(\''+ report.img_path +'\');" alt=""></div>'+
                        '</div>'+
                        '</div>'+
                        '<div class="col-md-6 map-bubble-desc">' +
                    '<p>'+report.comment+'</p>'+
                    '<p>'+report.created_at_datetime+'</p>'+
                    '</div></div></div>',
                };

            }
        }, that.map);
    };

    this.syncMarkersByTrackerAttachment = function (tracker) {
        that.removeMapLayerObjects('tracker_attachments');
        angular.forEach(tracker.attaches, function (attachment, index) {
            var marker_id = 'trackerAttachment_' + attachment.pivot.attachment_id;
            if (attachment.pivot.description.lat && attachment.pivot.description.lng) {
                attachment.img_path = (attachment.pivot.attachment_id) ? window.constants.ABS_URI + 'file/' + attachment.pivot.attachment_id : window.constants.ABS_URI + 'images/default_thumb_explore.png';
                attachment.content = (attachment.pivot.description.content != null && attachment.pivot.description.content != undefined) ? attachment.pivot.description.content : "";
                that.map.markers[marker_id] = {
                    lat: parseFloat(attachment.pivot.description.lat),
                    lng: parseFloat(attachment.pivot.description.lng),
                    icon: {
                        iconUrl: window.constants.ABS_URI + 'images/pin_tracker_attachment.png',
                        iconSize: [20, 20],
                        popupAnchor: [0, -10]
                    },
                    layer: 'tracker_attachments',
                    trackerId: tracker.id,
                    message: '<div class="map-bubble">'+
                        '<div class="row">'+
                        '<div class="col-md-6">'+
                        '<div class="map-bubble-thumbnail" >'+
                        '<div class="img" style="background-image: url(\''+ attachment.img_path +'\');" alt=""></div>'+
                        '</div>'+
                        '</div>'+
                        '<div class="col-md-6 map-bubble-desc">' +
                    '<p>'+ attachment.content+'</p>'+
                    '<p>'+ attachment.created_at +'</p>'+
                    '</div></div></div>',
                };
            }
        });
    };

    this.markerPopupSwitch = function(project_id, open_up) {
        if(that.map.markers[project_id])
        {
            that.map.center.lat = that.map.markers[project_id].lat;
            that.map.center.lng = that.map.markers[project_id].lng;
            that.map.markers[project_id].focus = open_up;
        }
    };

    this.enableReporterLocationUpdater = function(){
        that.updateReporterLocations();
        that.reporterUpdater = $interval(that.updateReporterLocations, 5000);
    };

    this.disableReporterLocationUpdater = function(){
        if(that.reporterUpdater){
            $interval.cancel(that.reporterUpdater);
        }
        that.reporterUpdater = undefined;
    };

    this.updateReporterLocations = function(){
        ExploreFactory.getReporterLocation().then(function(response){
            var reporters = response.data;
            var currentMarkerIds = [];

            angular.forEach(reporters, function(reporter, index){
                var markerId = 'reporter_'+reporter.device_id;
                currentMarkerIds.push(markerId);

                that.map.markers[markerId] = {
                    lat: reporter.lat,
                    lng: reporter.lng,
                    icon: {
                        iconUrl: window.constants.ABS_URI + 'images/pin_human.png',
                        iconSize: [20, 20],
                        popupAnchor: [0, -10]
                    },
                    layer: 'reporter_location',
                    message: '<div class="map-bubble">'+
                        '<p>Device ID: '+reporter.device_id.substring(1,10)+'</p>'+
                        '<p>Location updated at: '+reporter.created_at+'</p>'+
                    '</div>',
                };
            });

            that.cleanUpRemovedReporterLocations(currentMarkerIds);
        });
    };

    this.cleanUpRemovedReporterLocations = function(current_reporter_marker_ids){
        var markers = that.map.markers;
        angular.forEach(markers, function(marker, index){
            if(index.substring(0,9) == 'reporter_' &&
                current_reporter_marker_ids.indexOf(index) == -1
            ){
                delete that.map.markers[index];
            }
        });
    };

    this.initMap = function(mapLayers, defaultLayer) {
        angular.forEach(mapLayers, function(mapLayer) {
            that.map.layers.overlays[mapLayer.value] = {
                name: mapLayer.name,
                visible: (mapLayer.value == defaultLayer) ? true : false,
                type: 'group'
            };
        });
        that.map.layers.overlays.tracker_attachments = {
            name: 'Tracker attachments',
            visible: that.map.layers.overlays.tracker.visible,
            type: 'group'
        }
        return that.map;
    };
});

ExploreApp.controller('ExploreIndexCtrl', function($scope, MapService, ExploreFactory, $interval, $log) {
    $scope.enablingExtraConditionsLengthCalculator = function() {
        var enabling_fields_length = 0;

        if($scope.conditions.status) {
            enabling_fields_length++;
        }

        angular.forEach($scope.conditions.filterable_fields, function(value, key) {
            if(value)
            {
                enabling_fields_length++;
            }
        });

        var enabling_categories_length = 0;
        angular.forEach($scope.conditions.categories, function(value, key) {
            if(value)
            {
                enabling_categories_length++;
            }
        });
        $scope.enabling_fields_length = enabling_fields_length + enabling_categories_length;
    };

    $scope.searchProjects = function() {
        $scope.searchProcessing = true;
        $scope.enablingExtraConditionsLengthCalculator();

        var searchingBufferTime = 1500; //ms
        if($scope.coolDown)
        {
            $interval.cancel($scope.searchQueue);

            var orderBy = [];
            if($scope.conditions.orderBy)
            {
                orderBy.push($scope.conditions.orderBy);
            }

            ExploreFactory.searchProjects($scope.conditions, orderBy, $scope.filters.fields).then(function(response) {
                    $scope.projects = response.data.projects;
                    $scope.total_items = response.data.projects.length;
                    $scope.current_page = 1;
                    $scope.searchProcessing = false;
                    MapService.syncMapObjects(response.data.projects);
                })
                .catch(function(response) {
                    $scope.projects = null;
                    $scope.searchProcessing = false;
                });

            $scope.coolDown = false;
            $scope.coolDownTimer = $interval(function() {
                $scope.coolDown = true;
                $interval.cancel($scope.coolDownTimer)
            }, searchingBufferTime);
        }
        else
        {
            if(!$scope.searchQueuing)
            {
                $scope.searchQueuing = true;
                $scope.searchQueue = $interval(function() {
                    $scope.searchQueuing = false;
                    $scope.coolDown = true;
                    $scope.searchProjects();
                }, searchingBufferTime);
            }
        }
    };

    $scope.getCitizenReports = function() {
        ExploreFactory.getCitizenReports().then(function(response) {
                $scope.citizenReports = response.data;
                MapService.syncMarkersByCitizenReports($scope.citizenReports);
            })
            .catch(function(response) {
                $log.warn('get Citizen report XHR fail');
            });
    };

    $scope.getTrackers = function() {
        ExploreFactory.getTrackers().then(function(response) {
            $scope.trackers = response.data;
            MapService.syncPathByCoordinates($scope.trackers);
        })
        .catch(function(response) {
            $log.warn(response);
        });
    };

    $scope.markerPopupSwitch = function(projectId, openUp) {
        if($scope.popupBuffer)
        {
            $interval.cancel($scope.popupBuffer);
        }

        if(openUp)
        {
            $scope.popupBuffer = $interval(function(){
                if(projectId)
                {
                    $interval.cancel($scope.popupBuffer);
                    MapService.markerPopupSwitch(projectId, openUp);
                }
            }, 1000);
        }
        else
        {
            MapService.markerPopupSwitch(projectId, false);
        }
    };

    $scope.$on('leafletDirectivePath.click', function(event, path) {
        angular.forEach(MapService.map.paths, function(single_path, index) {
            if(single_path.speed_tracker) {
                delete MapService.map.paths[index];
            }
        });
        var path_idx = parseInt(path.modelName);
        if (!MapService.map.paths[path_idx]) {
            return false;
        }

        var tracker = $scope.trackers[MapService.map.paths[path_idx].trackerIndex];
        MapService.removeMapLayerObjects('tracker_attachments');
        MapService.syncMarkersByTrackerAttachment(tracker);

        var tracker_type = path.leafletObject.options.layer;
        var coordinate_groups = $scope.trackers[MapService.map.paths[path_idx].trackerIndex].path;

        coordinate_groups.map(function(coordinates, index){
            for (var i = 1; i <= coordinates.length - 1; i++) {
                var path = {
                    color: '#217c27',
                    weight: 6,
                    layer: tracker_type,
                    speed_tracker: true
                };
                path.latlngs = [{
                    lat: coordinates[i - 1][0],
                    lng: coordinates[i - 1][1]
                }, {
                    lat: coordinates[i][0],
                    lng: coordinates[i][1]
                }, ];

                if(tracker_type == 'tracker')
                {
                    var distance = Math.sqrt(
                        Math.pow(path.latlngs[0].lat - path.latlngs[1].lat, 2) +
                        Math.pow(path.latlngs[0].lng - path.latlngs[1].lng, 2)
                    );

                    distance *= 111;
                    var time = coordinates[i][2] - coordinates[i - 1][2];

                    if (time > 0)
                    {
                        var speed = distance / time * 3600;
                        path.color = getSpeedColor(tracker_type, speed);
                    }
                    else
                    {
                        continue;
                    }

                }

                path.message = $scope.trackers[MapService.map.paths[path_idx].trackerIndex].title;
                MapService.map.paths.push(path);
            }
        });
    });

    function getSpeedColor(type, value) {
        var color = "";
        if (value < 20) {
            color = '#FF0000';
        } else if (value < 40) {
            color = '#FF9200';
        } else if (value < 60) {
            color = '#FAFF00';
        } else if (value < 80) {
            color = '#71F829';
        } else if (value >= 80) {
            color = '#1fe6ff';
        }

        return color;
    }

	(function init() {
        $scope.rootContainer = window.init_data.rootContainer;
        $scope.statuses = window.init_data.statuses;
        $scope.mapOptions = window.init_data.map_options;
        $scope.lang = window.lang_trans;
        $scope.mode = 'map';
        $scope.cool_down = true;
        $scope.showAdvanceBox = false;
        $scope.projects_per_page = 10;
        $scope.conditions = {
            orderBy: 'updated_at',
        };

        $scope.map = MapService.initMap($scope.mapOptions, 'project');
        if ($scope.rootContainer != null) {
            ExploreFactory.getContainer($scope.rootContainer.id).then(function(response) {
                $scope.filters = response.data.filters || {};
                $scope.searchProjects();
            })
            .catch(function(response) {
                $log.warn('getContainer XHR fail');
            });
        }
        $scope.getCitizenReports();
        $scope.getTrackers();
	})();
});

ExploreApp.component('searchBar', {
    templateUrl: window.constants.ABS_URI + 'partials/explore/search-bar.html',
    bindings: {
        mode: '=',
        map: '=',
        containerName: '<',
        projects: '<',
        filters: '<',
        statuses: '<',
        regions: '<',
        conditions: '=',
        showAdvanceBox: '=',
        mapOptions:'<',
        searchProjects: '&',
        enablingFieldsLength: '<',
        lang:'<',
    },
    controller : function($log, ExploreFactory, MapService) {
        var scope = this;

        this.$onInit = function () {
            scope.filters = {};
            scope.regions = [];
            scope.fields = {};

            scope.initRegionFilters();
            scope.initFieldFilters();
        };

        this.initFieldFilters = function(){
            scope.conditions.fields = {};
        };

        this.initRegionFilters = function(){
            scope.conditions.regions = [];
            scope.updateRegions(-1);
        };

        this.updateRegions = function(triggerIndex) {
            // Initial root regions list, not going to trigger by user
            if (triggerIndex < 0) {
                ExploreFactory.getRootRegions()
                    .then(function(response){
                        if (response.data) {
                            scope.regions[0] = response.data;
                        }
                    })
                    .catch(function(response){
                        $log.warn('getRootRegions XHR fail');
                        scope.regions = [];
                    });
                return;
            }

            var triggerRegionId = scope.conditions.regions[triggerIndex];

            if (!triggerRegionId) {
                scope.cleanSubRegions(triggerIndex);
            } else {
                ExploreFactory.getRegions(triggerRegionId)
                    .then(function(response){
                        if (response.data) {
                            scope.regions[triggerIndex+1] = response.data.subregions;
                        }
                    })
                    .catch(function(response){
                        $log.warn('getRegions XHR fail');
                        delete scope.regions[triggerIndex+1];
                    });
            }
        };

        // Triggers for view
        this.regionChanged = function(triggerIndex) {
            scope.updateRegions(triggerIndex);
            scope.searchProjects();
        };

        this.cleanSubRegions = function(triggerIndex) {
            scope.regions = scope.regions.slice(0, triggerIndex+1);
            if (triggerIndex > 0) {
                scope.conditions.regions = scope.conditions.regions.slice(0, triggerIndex);
            } else {
                scope.conditions.regions = [];
            }
        };

        this.fieldChanged = function(field) {
            if (scope.fields[field.id]) {
                scope.conditions.fields[field.id] = {
                    id: field.id,
                    value: scope.fields[field.id],
                    filter_key: field.filter_key
                };
            } else {
                delete scope.conditions.fields[field.id];
            }
            scope.searchProjects();
        };

        this.mapLayerChanged = function (option) {
            if (option == "tracker") {
                MapService.map.layers.overlays.tracker_attachments.visible = MapService.map.layers.overlays.tracker.visible;
            }
            if (option == "reporter_location") {
                scope.checkReporterLocationUpdater();
            }
        };

        this.checkReporterLocationUpdater = function () {
            if (scope.map.layers.overlays.reporter_location.visible) {
                MapService.enableReporterLocationUpdater();
            } else {
                MapService.disableReporterLocationUpdater();
            }
        };
    },
});

ExploreApp.component('projectListOfMap', {
    templateUrl: window.constants.ABS_URI + 'partials/explore/project-list-of-map.html',
    bindings: {
        projects: '=',
        containerName: '<',
        conditions: '=',
        filters: '<',
        searchProcessing: '<',
        totalItems : '=',
        currentPage : '=',
        projectsPerPage: '<',
        searchProjects: '&',
        markerPopupSwitch: '=',
        lang:'<',
    },
    controller: function() {
        var scope = this;
        this.$onInit = function() {
            scope.loadingImage = window.constants.ABS_URI + 'images/animation/loader.gif';
            scope.absUri = window.constants.ABS_URI;
        };
    },
});

ExploreApp.component('projectListOfMapMobile', {
    templateUrl: window.constants.ABS_URI + 'partials/explore/project-list-of-map-mobile.html',
    bindings: {
        containerName: '<',
        projects: '=',
        filters: '<',
        totalItems : '=',
        conditions: '=',
        searchProjects: '&',
    },
    controller: function() {
        var scope = this;
        this.$onInit = function() {
            scope.currentPage = 1;
            scope.projectsPerPage = 1;
        };
    }
});

ExploreApp.component('projectList', {
    templateUrl: window.constants.ABS_URI + 'partials/explore/project-list.html',
    bindings: {
        mode: '=',
        containerName: '<',
        projects: '=',
        conditions: '=',
        filters: '<',
        totalItems : '=',
        currentPage : '=',
        projectsPerPage: '<',
        showRegions: '<',
        searchProjects: '&',
    },
    controller : function($timeout) {
        var scope = this;

        this.hotKeyClick = function() {
            scope.exploreListTable.scrollLeft += 100;
        };

        this.goToProject = function (projectId) {
            window.location.href = window.constants.ABS_URI + 'project/' + projectId;
        };

        this.$onInit = function() {
            scope.exploreListTable = angular.element('#explore-list-table')[0];
            scope.absUri = window.constants.ABS_URI;
        };

        this.showHotKey = function() {
            return scope.exploreListTable.scrollWidth > scope.exploreListTable.clientWidth;
        };
    }
});

ExploreApp.component('projectListMobile', {
    templateUrl: window.constants.ABS_URI + 'partials/explore/project-list-mobile.html',
    bindings: {
        mode: '=',
        containerName: '<',
        projects: '=',
        conditions: '=',
        filters: '<',
        totalItems : '=',
        currentPage : '=',
        projectsPerPage: '<',
        searchProjects: '&',
    },
    controller : function($timeout) {
        var scope = this;

        this.hotKeyClickMobile = function() {
            scope.exploreListTableMobile.scrollLeft += 100;
        };
        this.$onInit = function() {
            scope.exploreListTableMobile = angular.element('#explore-list-table-mobile')[0];
        };
        this.goToProject = function (projectId) {
            window.location.href = window.constants.ABS_URI + 'project/' + projectId;
        };
        this.showHotKeyMoblie = function() {
            return scope.exploreListTableMobile.scrollWidth > scope.exploreListTableMobile.clientWidth;
        };
    }
});

ExploreApp.component('modeSwitch', {
    templateUrl: window.constants.ABS_URI + 'partials/explore/mode-switch.html',
    bindings: {
        mode: '='
    }
});

ExploreApp.directive('popup', function($compile, $location, MapService) {
    return {
        restrict: "E",
        replace: true,
        scope: {
            index: "=",
        },
        link: function(scope, element, attrs) {
            scope.project = MapService.projects[scope.index];
            scope.project_link = window.constants.ABS_URI + 'project/' + scope.project.id;
        },
        templateUrl: window.constants.ABS_URI + 'partials/home-map-bubble.html'
    };
});
