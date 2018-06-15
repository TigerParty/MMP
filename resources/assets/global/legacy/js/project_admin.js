angular.module("ProjectAdminApp", ['ui.bootstrap', 'DatePicker', 'Helper', 'ngFileUpload', 'leaflet-directive'])

.config(function($interpolateProvider, $logProvider, $httpProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $logProvider.debugEnabled(false);
    $httpProvider.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
})

.filter('titleize', function() {
    return function(input) {
        input = input || '';
        return input.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    };
})

.factory('ProjectAdminFactory', function($http, Upload) {
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
        getForms: function() {
            return $http.get(window.constants.ABS_URI + 'admin/dync_form/api');
        },
        getProjects: function(conditions, pageNo, orderBy, limit = null) {
            var payload = {
                page: pageNo,
                conditions: conditions,
                order_by: orderBy,
                _token: window.constants.CSRF_TOKEN,
            };

            if (limit) {
                payload.limit = limit;
            }

            return $http.post(window.constants.ABS_URI + 'admin/project/api', payload);
        },
        getProjectShowData: function(id) {
            return $http.get(window.constants.ABS_URI + 'admin/project/' + id + '/api');
        },
        getFormCreateData: function(projectId, formId) {
            return $http.get(window.constants.ABS_URI + 'admin/project/' + projectId + '/form/' + formId + '/create/api');
        },
        getFormEditData: function(projectId, formId) {
            return $http.get(window.constants.ABS_URI + 'admin/project/' + projectId + '/form/' + formId + '/edit/api');
        },
        deleteProject: function(projectId) {
            return $http.delete(window.constants.ABS_URI + 'admin/project/' + projectId);
        },
        getRootProjectCreateInit: function() {
            return $http.get(window.constants.ABS_URI + 'admin/project/create/api');
        },
        getSubProjectCreateInit: function(parentId, containerId) {
            return $http.get(window.constants.ABS_URI + 'admin/project/'+ parentId +'/container/'+ containerId +'/subproject/create/api');
        },
        getProjectEditInit: function(projectId) {
            return $http.get(window.constants.ABS_URI + 'admin/project/' + projectId + '/edit/api');
        },
        uploadAttachment: function(attach){
            return Upload.upload({
                url: window.constants.ABS_URI + 'file/upload',
                method: 'POST',
                file: attach,
                fields: {
                    _token: window.constants.CSRF_TOKEN,
                }
            });
        },
        postProject: function(project) {
            return $http.post(window.constants.ABS_URI + 'admin/project', {
                project: project,
                _token: window.constants.CSRF_TOKEN,
            });
        },
        updateProject: function(projectId, project) {
            return $http.put(window.constants.ABS_URI + 'admin/project/' + projectId, {
                project: project,
                _token: window.constants.CSRF_TOKEN,
            });
        },
        updateProjectValues: function(project, formId, formFieldValues) {
            return $http.put(window.constants.ABS_URI + 'admin/project/' + project.id + '/form/' + formId, {
                _token: window.constants.CSRF_TOKEN,
                project: project,
                formFieldValues: formFieldValues,
            });
        },
        rotateImage: function(attachId) {
            return $http.post(window.constants.ABS_URI + 'webapi/rotate', {
                _token: window.constants.CSRF_TOKEN,
                ath_id: attachId
            });
        },
        storeForm: function(formData) {
            return $http.post(window.constants.ABS_URI + 'admin/project/' + formData.projectId + '/form/' + formData.formId, {
                _token: window.constants.CSRF_TOKEN,
                formFieldValues: formData.formFieldValues,
                mediaGroups: formData.mediaGroups
            });
        },
        updateForm: function(formData) {
            return $http.put(window.constants.ABS_URI + 'admin/project/' + formData.project_id + '/form/' + formData.form_id, {
                _token: window.constants.CSRF_TOKEN,
                formFieldValues: formData.form_field_values,
                mediaGroups: formData.media_groups
            });
        },
        getRootProjectBatchCreatingInit: function() {
            return $http.get(window.constants.ABS_URI + 'admin/project/create/batch/api');
        },
        getSubProjectBatchCreatingInit: function(parentId, containerId) {
            return $http.get(window.constants.ABS_URI + 'admin/project/'+ parentId +'/container/'+ containerId +'/subproject/create/batch/api');
        },
        importExcel: function(excelFile, canceller) {
            return Upload.upload({
                url: window.constants.ABS_URI + 'admin/project/import/excel',
                timeout: canceller.promise,
                method: 'POST',
                file: excelFile,
                fields: {
                    _token: window.constants.CSRF_TOKEN,
                }
            });
        },
        exportRootProjectExcel: function(formId) {
            var requestHeader = {
                responseType: 'arraybuffer'
            };
            return $http.get(window.constants.ABS_URI + 'admin/project/excel?form_id=' + formId, requestHeader);
        },
        exportSubrojectExcel: function(parentId, containerId, formId) {
            var requestHeader = {
                responseType: 'arraybuffer'
            };
            return $http.get(window.constants.ABS_URI + 'admin/project/' + parentId + '/container/' + containerId + '/subproject/excel?form_id=' + formId, requestHeader);
        }
    };
})

.service('FieldService', function () {
    var that = this;

    this.switchTemplateCondition = function(field) {

        switch(field.template_name) {
            case 'partials/tp_check_box.html':
                if( field.value  == 'yes') {
                    field.checked = true;
                }
                else {
                    field.checked = false;
                }
                break;
            case 'partials/tp_check_box_group.html':
            case 'partials/tp_drop_down_list.html':
            case 'partials/tp_radio_button.html':
            case 'partials/tp_text_area.html':
            case 'partials/tp_text_box.html':
                break;
        }
        field.templateUrl = window.constants.ABS_URI + field.template_name;
        return field;
    };
})

.service('MapService', function() {
    var that = this;

    this.map = {
        center: window.constants.INNER_MAP_CENTER,
        defaults: {
            tileLayer: 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            zoomControlPosition: 'topright',
            tileLayerOptions: {
                opacity: 0.9,
                detectRetina: true,
                reuseTiles: true,
            },
            scrollWheelZoom: false
        },
        markers: {},
        events: {
            map: {
                enable: ['click'],
                logic: 'emit'
            }
        }
    };

    this.updateMarker = function(lat, lng) {
        that.map.markers = {};
        if(lat && lng) {
            that.map.center.lat = lat;
            that.map.center.lng = lng;
            that.map.markers['project_location'] = {
                lat: lat,
                lng: lng,
                icon: {
                    iconUrl: window.constants.ABS_URI + 'images/icon/pin.png',
                    iconSize: [20, 20],
                    popupAnchor: [0 , -10]
                }
            };
        }
    };
})

.controller('RootIndexCtrl', function ($log, $timeout, $uibModal, ProjectAdminFactory) {
    scope = this;

    // Init variables
    scope.conditions = {
        orderBy: 'title'
    };

    // Private helpers
    this.cleanEmptyConditions = function(){
        $log.debug('cleanEmptyCondtions called.');
        if (scope.conditions.keyword == '') {
            delete scope.conditions.keyword;
        }

        if (!scope.conditions.form) {
            delete scope.conditions.form;
        }
    };

    this.updateProjects = function(conditions, currentPage, orderBy){
        $log.debug('updateProjects called.');
        ProjectAdminFactory.getProjects(conditions, currentPage, orderBy)
            .then(function(response){
                scope.projects = response.data.page_projects;
                scope.itemsCount = response.data.projects_count;
                scope.itemFormFields = response.data.form_fields;
            })
            .catch(function(response){
                $log.warning('updateProjects XHR fail');
                scope.projects = [];
                scope.itemsCount = 0;
            });
    };

    // Component callbacks
    this.onConditionChange = function() {
        // Reset current Page and query with it if only filter conditions changed
        $log.debug('onConditionChange called.');
        if (scope.perviousPage != scope.currentPage) {
            scope.perviousPage = scope.currentPage;
        } else {
            scope.currentPage = 0;
            $log.debug('onConditionChange: reset currentPage to 0.');
        }

        scope.cleanEmptyConditions();
        var orderBy = [];
        if(scope.conditions.orderBy)
        {
            orderBy.push(scope.conditions.orderBy);
        }
        scope.updateProjects(scope.conditions, scope.currentPage, orderBy);
    };

    this.onRefresh = function() {
        scope.updateProjects(scope.conditions, scope.currentPage);
    };

    // Initilize
    (function init() {
        scope.perviousPage = NaN;
        scope.currentPage = 0;
        scope.itemsCount = 0;
        scope.mode = 'show';

        scope.itemsPerPage = window.init_data.items_per_page || 10;
        scope.containerId = window.init_data.container_id;
        scope.conditions.form = window.init_data.container_form_id || 0;

        // Init forms for panination-bar dropdownlist
        ProjectAdminFactory.getForms()
            .then(function(response){
                scope.forms = response.data;
            })
            .catch(function(response){
                $log.warning('getForms XHR fail');
                scope.forms = [];
            });

        // Init filters for filter-bar
        ProjectAdminFactory.getContainer(scope.containerId)
            .then(function(response){
                scope.filters = response.data.filters || {};
            })
            .catch(function(response){
                $log.warn('getContainer XHR fail');
                scope.filters = {};
            });


        $timeout(function(){
            scope.updateProjects(scope.conditions, scope.currentPage);
        }, 50);
    })();
})

.controller('ShowCtrl', function ($log, $timeout, ProjectAdminFactory, HelperFactory) {
    scope = this;

    var scopeConfig = {
        fetchDelay: 1500,
        itemsPerPage: 10,
    };

    this.initSubcontainerValues = function(index) {
        $log.debug("Updating subcontainer - Id:" + scope.subcontainers[index].id);

        var conditions = scope.subcontainers[index].conditions;
        ProjectAdminFactory.getProjects(conditions, 0, ['title'], scope.itemsPerPage)
            .then(function(response){
                scope.subcontainers[index].itemFormFields = response.data.form_fields;
                scope.subcontainers[index].projects = response.data.page_projects;
                scope.subcontainers[index].projectsCount = response.data.projects_count;
                if (index+1 < scope.subcontainers.length){
                    $timeout(function(){
                        scope.initSubcontainerValues(index+1)
                    }, scopeConfig.fetchDelay);
                }
            })
            .catch(function(error){
                $log.error("Updating subcontainer fail - index:" + index);
            });
    };

    this.initSubcontainerFilters = function(index) {
        $log.debug("Updating subcontainer filter: " + scope.subcontainers[index])
        //TODO
    };

    this.cleanSubcontainerEmptyConditions = function(index){
        if (scope.subcontainers[index].conditions.keyword == '') {
            delete scope.conditions.keyword;
        }

        if (!scope.subcontainers[index].conditions.form) {
            delete scope.conditions.form;
        }
    };

    this.onSubcontainerConditionChange = function(index) {
        $log.debug("Subcontainer filter conditions changed: " + index);

        var container = scope.subcontainers[index];

        if (container.perviousPage != container.currentPage) {
            container.perviousPage = container.currentPage;
        } else {
            container.currentPage = 0;
            $log.debug('onContainerConditionChange: reset currentPage to 0 for index: ' + index);
        }

        scope.cleanSubcontainerEmptyConditions(index);

        var orderBy = [];
        if(container.conditions.orderBy)
        {
            orderBy.push(container.conditions.orderBy);
        }

        ProjectAdminFactory.getProjects(container.conditions, container.currentPage, orderBy, scope.itemsPerPage)
            .then(function(response){
                container.projects = response.data.page_projects;
                container.itemsCount = response.data.projects_count;
                container.itemFormFields = response.data.form_fields;
            })
            .catch(function(response){
                $log.warning('updateProjects XHR fail');
                container.projects = [];
                container.itemsCount = 0;
            });
    };

    this.onSubcontainerRefresh = function(index) {
        $log.debug("Subcontainer refresh: " + index);
        var container = scope.subcontainers[index];

        var orderBy = [];
        if(container.conditions.orderBy)
        {
            orderBy.push(container.conditions.orderBy);
        }

        ProjectAdminFactory.getProjects(container.conditions, container.currentPage, orderBy, scope.itemsPerPage)
            .then(function(response){
                container.projects = response.data.page_projects;
                container.itemsCount = response.data.projects_count;
                container.itemFormFields = response.data.form_fields;
            })
            .catch(function(response){
                $log.warning('updateProjects XHR fail');
                container.projects = [];
                container.itemsCount = 0;
            });
    };

    this.onFormSelected = function(form){
        scope.selectedForm = form;
    };

    // Initilize
    (function init() {
        scope.debug = false;
        scope.absUri = window.constants.ABS_URI;
        scope.projectId = window.init_data.projectId;
        scope.itemsPerPage = scopeConfig.itemsPerPage;

        // Fetch page initial XHR
        ProjectAdminFactory.getProjectShowData(scope.projectId)
            .then(function(response) {
                scope.projectData = response.data;
                scope.breadcrumbs = response.data.breadcrumbs;

                angular.forEach(scope.breadcrumbs, function(breadcrumb, index) {

                    if (breadcrumb.type == 'project') {
                        breadcrumb.url = scope.absUri + 'admin/project/' + breadcrumb.id;
                    } else if (index == 0) {
                        breadcrumb.url = scope.absUri + 'admin/project/';
                    }
                });

                angular.forEach(scope.projectData.forms, function(form) {
                    angular.forEach(form.values, function(field) {
                        if(field.field_template_key == 'check_box_group')
                        {
                            try
                            {
                                field.value = JSON.parse(field.value).join(", ");
                            }
                            catch(err)
                            {
                                field.value = null;
                            }
                        }
                    });
                });

                // Initial subcontainer lists
                angular.forEach(response.data.subcontainers, function(container, index) {
                    container.mode = 'show';
                    container.currentPage = 0;
                    container.perviousPage = NaN;
                    container.conditions = {
                        parent_id: scope.projectId,
                        container_id: container.id,
                        form: container.form_id,
                        projects: [],
                        orderBy: 'title',
                    };
                });

                scope.subcontainers = response.data.subcontainers;

                // Async XHR fetching container page projects with delay
                if (scope.subcontainers.length > 0) {
                    scope.initSubcontainerValues(0);
                }
            })
            .catch(function(error) {
                $log.error('Project show initial XHR failed');
                $log.error(error);
                scope.projectData = null;
                scope.subcontainers = [];
            }).finally(function(){
                // Initial forms dropdown
                ProjectAdminFactory.getForms()
                    .then(function(response){
                        scope.forms = response.data;
                    })
                    .catch(function(response){
                        $log.warning('getForms XHR fail');
                        scope.forms = [];
                    })
                    .finally(function(){
                        //-- set form font style
                        scope.forms.forEach(function(form){
                            var result = scope.projectData.forms.filter(function(existForm){
                                return existForm.id == form.id;
                            });

                            if (result.length > 0) {
                                form.fontStyle = "bold";
                                form.goMode = "edit";
                            } else {
                                form.goMode = "create";
                            }
                        });

                        scope.selectedForm = {
                            name: "- Select Form -"
                        };
                    });
            });
    })();
})

.controller('CreateCtrl', function ($log, ProjectAdminFactory, MapService, $scope, $uibModal) {
    scope = this;

    scope.regions = [];
    scope.selectedRegions = [];
    scope.defaultImgIds = [];
    scope.coverImgIds = [];
    scope.project = {
        fields: {},
        attach_ids: [],
         // Selected region must been hold by array
         // or the un-select clean function will fail to clean it up
        regions: [],
    };

    this.updateRegions = function(triggerIndex) {
        // Initial root regions list, not going to trigger by user
        if (triggerIndex < 0) {
            ProjectAdminFactory.getRootRegions()
                .then(function(response){
                    scope.regions[0] = response.data;
                })
                .catch(function(response){
                    $log.warn('getRootRegions XHR fail');
                    scope.regions = [];
                });
            return;
        }

        var triggerRegionId = scope.project.regions[triggerIndex];

        if (!triggerRegionId) {
            scope.cleanSubRegions(triggerIndex);
        } else {
            ProjectAdminFactory.getRegions(triggerRegionId)
                .then(function(response){
                    if (response.data) {
                        scope.regions[triggerIndex+1] = response.data.subregions;
                    }
                })
                .catch(function(response){
                    $log.warning('getRegions XHR fail');
                    delete scope.regions[triggerIndex+1];
                });
        }
    };

    this.cleanSubRegions = function(triggerIndex) {
        scope.regions = scope.regions.slice(0, triggerIndex+1);
        if (triggerIndex > 0) {
            scope.project.regions = scope.project.regions.slice(0, triggerIndex);
        } else {
            scope.project.regions = [];
        }
    };

    this.onFieldChange = function(fieldId) {
        $log.debug("Field " + fieldId + " updated.");
    };

    this.initRootProjectCreating = function(){
        ProjectAdminFactory.getRootProjectCreateInit()
            .then(function(response){
                scope.project.parent_id = response.data.project.parent_id;
                scope.project.container_id = response.data.project.container_id;
                scope.project.view_level_id = response.data.project.view_level_id;
                scope.project.edit_level_id = response.data.project.edit_level_id;

                scope.statuses = response.data.statuses;
                scope.levels = response.data.permission_levels;
                scope.fields = response.data.fields;
                scope.container = response.data.container;
                scope.regionLabels = response.data.region_labels;

                scope.statusVisible = true;

                if (scope.regionLabels) {
                    scope.updateRegions(-1);
                }

                scope.breadcrumbs = response.data.breadcrumbs;
                angular.forEach(scope.breadcrumbs, function(breadcrumb, index) {
                    if (breadcrumb.type == 'project') {
                        breadcrumb.url = scope.absUri + 'admin/project/' + breadcrumb.id;
                    } else if (index == 0) {
                        breadcrumb.url = scope.absUri + 'admin/project/';
                    }
                });

                scope.statuses.forEach(function(status){
                    if (status.default == 1) {
                        scope.project.status_id = status.id;
                    }
                });
            })
            .catch(function(err){
                $log.error('Init project create XHR fail.');
            });
    };

    this.initSubProjectCreating = function(){
        $log.debug('Subproject creating');
        var uriParentId = window.init_data.parent_id;
        var uriContainerId = window.init_data.container_id;

        ProjectAdminFactory.getSubProjectCreateInit(uriParentId, uriContainerId)
            .then(function(response){
                scope.project.parent_id = response.data.project.parent_id;
                scope.project.container_id = response.data.project.container_id;
                scope.project.view_level_id = response.data.project.view_level_id;
                scope.project.edit_level_id = response.data.project.edit_level_id;

                scope.statuses = response.data.statuses;
                scope.levels = response.data.permission_levels;
                scope.fields = response.data.fields;
                scope.container = response.data.container;
                scope.regionLabels = response.data.region_labels;

                scope.statusVisible = false;

                if (scope.regionLabels) {
                    scope.updateRegions(-1);
                }

                scope.breadcrumbs = response.data.breadcrumbs;
                angular.forEach(scope.breadcrumbs, function(breadcrumb, index) {
                    if (breadcrumb.type == 'project') {
                        breadcrumb.url = scope.absUri + 'admin/project/' + breadcrumb.id;
                    } else if (index == 0) {
                        breadcrumb.url = scope.absUri + 'admin/project/';
                    }
                });

                scope.statuses.forEach(function(status){
                    if (status.default == 1) {
                        scope.project.status_id = status.id;
                    }
                });
            })
            .catch(function(err){
                $log.error('Init subproject create XHR fail.');
            });
    };

    $scope.$on('leafletDirectiveMap.click', function(event, args) {
        var leafEvent = args.leafletEvent;
        scope.project.lat = leafEvent.latlng.lat;
        scope.project.lng = leafEvent.latlng.lng;

        MapService.updateMarker(leafEvent.latlng.lat, leafEvent.latlng.lng);
    });

    this.updateMapLocation = function() {
        if(scope.project.lat && scope.project.lng) {
            MapService.updateMarker(scope.project.lat, scope.project.lng);
        }
    };

    this.submitForm = function() {
        ProjectAdminFactory.postProject(scope.project)
        .then(function(response){
            $log.debug('Submit project success');
            // Redirect to project admin show once update successed
            window.location = response.data.path;
        })
        .catch(function(error){
            $log.debug('Submit project fail: '+error);
            scope.errors = error.data;
        });
    };

    this.goBack = function () {
        window.history.back();
    };

    this.onDefaultImgUploadFinished = function() {
        if (scope.defaultImgIds.length > 0){
            scope.project.default_img_id = scope.defaultImgIds[0];
        }
    };

    this.onCoverImgUploadFinished = function() {
        if (scope.coverImgIds.length > 0){
            scope.project.cover_image_id = scope.coverImgIds[0];
        }
    };

    this.onRemoveCoverImageBtnClick = function() {
        $uibModal.open({
            animation: true,
            component: 'confirmModal',
            windowClass: 'top-30-percent',
            resolve: {
                headerMsg: function(){
                    return 'Remove cover image?';
                },
                bodyMsg: function(){
                    return 'Confirm remove cover image?';
                },
            },
        })
        .result.then(function(obj){
            $log.debug("onRemoveCoverImageOkClick");
            scope.project.cover_image_id = null;
        }, function(obj){
            $log.debug("onRemoveCoverImageCancelClick");
        });
    };

    // Initilize
    (function init() {
        scope.createdBy = window.init_data.created_by;
        scope.absUri = window.constants.ABS_URI;

        // Make sure variable type compaitable with dynamicField component
        scope.project.created_at = new Date().toString();
        scope.project.updated_at = new Date().toString();

        if (!window.init_data.parent_id) {
            // Creating root project
            scope.initRootProjectCreating();
        } else {
            // Creating sub project
            scope.initSubProjectCreating();
        }

        scope.map = MapService.map;
    })();
})

.controller('EditCtrl', function ($log, ProjectAdminFactory, MapService, $scope, $uibModal) {
    scope = this;

    scope.regions = [];
    scope.selectedRegions = [];
    scope.defaultImgIds = [];
    scope.coverImgIds = [];
    scope.project = {
        fields: {},
        attach_ids: [],
        regions: [],
    };

    this.updateRegions = function(triggerIndex) {
        // Initial root regions list, not going to trigger by user
        if (triggerIndex < 0) {
            ProjectAdminFactory.getRootRegions()
                .then(function(response){
                    scope.regions[0] = response.data;
                })
                .catch(function(response){
                    $log.warn('getRootRegions XHR fail');
                    scope.regions = [];
                });
            return;
        }

        var triggerRegionId = scope.project.regions[triggerIndex];

        if (!triggerRegionId) {
            scope.cleanSubRegions(triggerIndex);
        } else {
            ProjectAdminFactory.getRegions(triggerRegionId)
                .then(function(response){
                    if (response.data) {
                        scope.regions[triggerIndex+1] = response.data.subregions;
                    }
                })
                .catch(function(response){
                    $log.warning('getRegions XHR fail');
                    delete scope.regions[triggerIndex+1];
                });
        }
    };

    this.cleanSubRegions = function(triggerIndex) {
        scope.regions = scope.regions.slice(0, triggerIndex+1);
        if (triggerIndex > 0) {
            scope.project.regions = scope.project.regions.slice(0, triggerIndex);
        } else {
            scope.project.regions = [];
        }
    };

    this.initProjectEditing = function(projectId) {

        ProjectAdminFactory.getProjectEditInit(projectId)
            .then(function(response){
                scope.project = response.data.project;
                if(scope.project.lat && scope.project.lng) {
                    MapService.updateMarker(scope.project.lat, scope.project.lng);
                }

                scope.statuses = response.data.statuses;
                scope.levels = response.data.permission_levels;
                scope.container = response.data.container;
                scope.regionLabels = response.data.region_labels;
                scope.breadcrumbs = response.data.breadcrumbs;

                scope.statusVisible = scope.container.parent_id ? false : true;

                angular.forEach(scope.regionLabels, function(regionLabel, index) {
                    scope.updateRegions(index-1);
                });

                angular.forEach(scope.breadcrumbs, function(breadcrumb, index) {
                    if (breadcrumb.type == 'project') {
                        breadcrumb.url = scope.absUri + 'admin/project/' + breadcrumb.id;
                    } else if (index == 0) {
                        breadcrumb.url = scope.absUri + 'admin/project/';
                    }
                });
            })
            .catch(function(error){
                $log.error('Init project edit XHR fail.');
            });
    };

    $scope.$on('leafletDirectiveMap.click', function(event, args) {
        var leafEvent = args.leafletEvent;
        scope.project.lat = leafEvent.latlng.lat;
        scope.project.lng = leafEvent.latlng.lng;

        MapService.updateMarker(leafEvent.latlng.lat, leafEvent.latlng.lng);
    });

    this.updateMapLocation = function() {
        if(scope.project.lat && scope.project.lng) {
            MapService.updateMarker(scope.project.lat, scope.project.lng);
        }
    };

    this.submitForm = function() {
        ProjectAdminFactory.updateProject(scope.projectId, scope.project)
        .then(function(response){
            $log.debug('Submit project success');
            // Redirect to project admin show once update successed
            window.location.href = window.constants.ABS_URI + 'admin/project/' + scope.projectId;
        })
        .catch(function(error){
            $log.debug('Submit project fail: '+error);
            scope.errors = error.data;
        });
    };

    this.cancelForm = function() {
        // Not sure what to do, redirect back to pervious page for now
        window.history.back();
    };

    this.onDefaultImgUploadFinished = function() {
        if (scope.defaultImgIds.length > 0){
            scope.project.default_img_id = scope.defaultImgIds[0];
        }
    };

    this.onCoverImgUploadFinished = function() {
        if (scope.coverImgIds.length > 0){
            scope.project.cover_image_id = scope.coverImgIds[0];
        }
    };

    this.onRemoveCoverImageBtnClick = function() {
        $uibModal.open({
            animation: true,
            component: 'confirmModal',
            windowClass: 'top-30-percent',
            resolve: {
                headerMsg: function(){
                    return 'Remove cover image?';
                },
                bodyMsg: function(){
                    return 'Confirm remove cover image?';
                },
            },
        })
        .result.then(function(obj){
            $log.debug("onRemoveCoverImageOkClick");
            scope.project.cover_image_id = null;
        }, function(obj){
            $log.debug("onRemoveCoverImageCancelClick");
        });
    };

    (function init() {
        var projectId = window.init_data.project_id;

        scope.projectId = projectId;
        scope.absUri = window.constants.ABS_URI;
        scope.initProjectEditing(projectId);
        scope.map = MapService.map;
    })();
})

.controller('FormCreateCtrl', ["$log", "$uibModal", "ProjectAdminFactory", "HelperFactory", "$filter",
    function ($log, $uibModal, ProjectAdminFactory, HelperFactory, $filter) {
        var scope = this;

        scope.uploadCheck = function() {
            if (scope.newPhotos.length > 0){
                scope.newPhotos.forEach(function(photo){
                    if (photo.mime.substring(0, 5) == 'image'){
                        var image = {
                            attachment_id: photo.id,
                            attachment_path: photo.asset_path,
                            attachment_type: photo.mime,
                            attached_at: null,
                            description: {
                                header: null,
                                content: null
                            }
                        };

                        if(scope.formData.mediaGroups.length === 0 ||
                           scope.formData.mediaGroups[scope.formData.mediaGroups.length-1].attached_at != $filter('date')(new Date(), 'MMMM, yyyy')) {
                            scope.formData.mediaGroups.push({
                                attached_at: $filter('date')(new Date(), 'MMMM, yyyy'),
                                items: [image]
                            });
                        } else {
                            scope.formData.mediaGroups[scope.formData.mediaGroups.length-1].items.push(image);
                        }
                    }
                });
            }
            scope.newPhotoIds = [];
            scope.newPhotos = [];
        };

        scope.submitForm = function() {
            ProjectAdminFactory.storeForm(scope.formData)
                .then(function(response) {
                    $log.debug('Submit form values success');
                    // Redirect to project admin show once store successed
                    window.location.href = window.constants.ABS_URI + 'admin/project/' + scope.projectId;
                })
                .catch(function(error) {
                    $log.debug('Submit form values fail: '+error);
                    scope.errors = error.data;
                });
        };

        scope.cancelForm = function() {
            window.history.back();
        };

        // Initilize
        (function init() {
            scope.projectId = window.init_data.projectId;
            scope.formId = window.init_data.formId;
            scope.absUri = window.constants.ABS_URI;

            ProjectAdminFactory.getFormCreateData(scope.projectId, scope.formId)
                .then(function(response) {
                    scope.formData = response.data;
                    scope.breadcrumbs = response.data.breadcrumbs;

                    angular.forEach(scope.breadcrumbs, function(breadcrumb, index) {
                        if (breadcrumb.type == 'project') {
                            breadcrumb.url = scope.absUri + 'admin/project/' + breadcrumb.id;
                        } else if (index === 0) {
                            breadcrumb.url = scope.absUri + 'admin/project/';
                        }
                    });
                })
                .catch(function(response) {
                    scope.formData = {};
                })
                .finally(function(){
                    scope.formData.mediaGroups = [];
                });
        })();
    }
])

.controller('FormEditCtrl', function ($log, $uibModal, ProjectAdminFactory, HelperFactory, $filter) {
    scope = this;

    this.uploadCheck = function() {
        if(scope.newPhotos.length > 0) {
            scope.newPhotos.forEach(function(photo){
                if(photo.mime.substring(0,5) == 'image')
                {
                    var image = {
                        attachment_id: photo.id,
                        attachment_path: photo.asset_path,
                        attachment_type: photo.mime,
                        attached_at: null,
                        description: {
                            header: null,
                            content: null
                        }
                    };

                    if(scope.formData.media_groups.length == 0 ||
                        scope.formData.media_groups[scope.formData.media_groups.length-1].attached_at != $filter('date')(new Date(), 'MMMM, yyyy'))
                    {
                        scope.formData.media_groups.push({
                            attached_at: $filter('date')(new Date(), 'MMMM, yyyy'),
                            items: [image]
                        });
                    }
                    else
                    {
                        scope.formData.media_groups[scope.formData.media_groups.length-1].items.push(image);
                    }
                }
            });
        }
        scope.newPhotoIds = [];
        scope.newPhotos = [];
    };

    this.updateForm = function() {
        ProjectAdminFactory.updateForm(scope.formData)
            .then(function(response) {
                $log.debug('Submit form values success');
                // Redirect to project admin show once store successed
                window.location.href = window.constants.ABS_URI + 'admin/project/' + scope.projectId;
            })
            .catch(function(error) {
                $log.debug('Submit form values fail: '+error);
                scope.errors = error.data;
            });
    };

    this.cancelForm = function() {
        // Not sure what to do, redirect back to pervious page for now
        window.history.back();
    };

    // Initilize
    (function init() {
        scope.projectId = window.init_data.projectId;
        scope.formId = window.init_data.formId;
        scope.absUri = window.constants.ABS_URI;

        ProjectAdminFactory.getFormEditData(scope.projectId, scope.formId)
            .then(function(response) {
                scope.formData = response.data;
                scope.breadcrumbs = response.data.breadcrumbs;

                angular.forEach(scope.breadcrumbs, function(breadcrumb, index) {
                    if (breadcrumb.type == 'project') {
                        breadcrumb.url = scope.absUri + 'admin/project/' + breadcrumb.id;
                    } else if (index == 0) {
                        breadcrumb.url = scope.absUri + 'admin/project/';
                    }
                });
            })
            .catch(function(response) {
                scope.formData = {};
            });
    })();
})

.controller('BatchCtrl', function($log, ProjectAdminFactory, $timeout, $uibModal) {
    scope = this;

    this.initProjects = function(parent_id = null) {
        scope.projects = [];
        for (var i = scope.initProjectRowsCount; i > 0; i--) {
            var project = {
                title: null,
                parent_id: parent_id,
                container_id: scope.containerId,
                supervisor: null,
                regions: [],
                regionOptions: [scope.regionOptions[0]]
            };

            scope.projects.push(project);
        }
    };

    this.regionChanged = function(project, regionIndex) {
        var regionLevel = scope.regionLabels.length;

        if(regionIndex+1 != regionLevel)
        {
            project.regions.splice(regionIndex+1, regionLevel-regionIndex+1);
            project.regionOptions.splice(regionIndex+1, regionLevel-regionIndex+1);

            if(project.regions[regionIndex])
            {
                ProjectAdminFactory.getRegions(project.regions[regionIndex])
                    .then(function(response) {
                        project.regionOptions[regionIndex+1] = response.data.subregions;
                    })
                    .catch(function(response) {
                        //
                    });
            }
        }
        scope.autoFillSupervisor(project);
    };

    this.initRootProjectBatchCreating = function() {
        ProjectAdminFactory.getRootProjectBatchCreatingInit()
            .then(function(response) {
                scope.container = response.data.container;
                scope.forms = response.data.forms;
                scope.regionLabels = response.data.region_labels;
                scope.regionOptions = response.data.region_options;
                for (var i = 0; i <= scope.forms.length - 1; i++) {
                    if(scope.forms[i].id == scope.container.form_id)
                    {
                        scope.selectedForm = scope.forms[i];
                        break;
                    }
                }

                scope.breadcrumbs = response.data.breadcrumbs;
                angular.forEach(scope.breadcrumbs, function(breadcrumb, index) {
                    if (breadcrumb.type == 'project') {
                        breadcrumb.url = scope.absUri + 'admin/project/' + breadcrumb.id;
                    } else if (index == 0) {
                        breadcrumb.url = scope.absUri + 'admin/project/';
                    }
                });

                scope.initProjects();
                scope.selectedFormChanged();
            })
            .catch(function(response) {
                //
            });
    };

    this.initSubProjectBatchCreating = function() {
        ProjectAdminFactory.getSubProjectBatchCreatingInit(scope.parentId, scope.containerId)
            .then(function(response) {
                scope.container = response.data.container;
                scope.forms = response.data.forms;
                scope.regionLabels = response.data.region_labels;
                scope.regionOptions = response.data.region_options;
                for (var i = 0; i <= scope.forms.length - 1; i++) {
                    if(scope.forms[i].id == scope.container.form_id)
                    {
                        scope.selectedForm = scope.forms[i];
                        break;
                    }
                }

                scope.breadcrumbs = response.data.breadcrumbs;
                angular.forEach(scope.breadcrumbs, function(breadcrumb, index) {
                    if (breadcrumb.type == 'project') {
                        breadcrumb.url = scope.absUri + 'admin/project/' + breadcrumb.id;
                    } else if (index == 0) {
                        breadcrumb.url = scope.absUri + 'admin/project/';
                    }
                });

                scope.initProjects(scope.parentId);
                scope.selectedFormChanged();
            })
            .catch(function(response) {
                //
            });
    };

    this.singleRowSubmit = function(index, submitSuccessCount = 0, submitFailCount = 0) {
        // Stop Submit
        scope.submitSuccessCount = submitSuccessCount;
        scope.submitFailCount = submitFailCount;
        if(index >= scope.initProjectRowsCount ||
            scope.submitCancel ||
            scope.requestBuffering)
        {
            if(index >= scope.initProjectRowsCount && !scope.requestBuffering && scope.allSubmitSuccess)
            {
                if(scope.parentId)
                {
                    window.location.href = scope.absUri + 'admin/project/' + scope.parentId;
                }
                else
                {
                    window.location.href = scope.absUri + 'admin/project';
                }
            }
            scope.submitCancel = false;
            return false;
        }

        if(!scope.projects[index].submitSuccess &&
            (scope.projects[index].title || scope.projects[index].regions.length > 0))
        {
            // scope.projects[index].submitStatusColor = '#fffdc4';
            scope.requestBuffering = true;

            if(!scope.validator(scope.projects[index]))
            {
                scope.projects[index].submitSuccess = false;
                // scope.projects[index].submitStatusColor = '#ffcec4';
                scope.submitFailCount += 1;
                $timeout(function(){
                    scope.requestBuffering = false;
                    scope.allSubmitSuccess = false;
                    scope.singleRowSubmit(index + 1, scope.submitSuccessCount, scope.submitFailCount);
                }, 2000);
                return false;
            }

            ProjectAdminFactory.postProject(scope.projects[index])
                .then(function(response) {
                    scope.projects[index].submitSuccess = true;
                    scope.projects[index].submitStatusColor = '#c6ffd0';
                    scope.submitSuccessCount += 1;
                    $timeout(function(){
                        scope.requestBuffering = false;
                        scope.singleRowSubmit(index + 1, scope.submitSuccessCount, scope.submitFailCount);
                    }, 2000);
                })
                .catch(function(response) {
                    scope.projects[index].submitSuccess = false;
                    scope.submitFailCount += 1;
                    if(response.data['project.title'])
                    {
                        scope.projects[index].errorColumns.title.push(scope.container.name + ' is duplicated!');
                    }
                    else
                    {
                        scope.projects[index].submitStatusColor = '#ffcec4';
                    }
                    $timeout(function(){
                        scope.requestBuffering = false;
                        scope.allSubmitSuccess = false;
                        scope.singleRowSubmit(index + 1, scope.submitSuccessCount, scope.submitFailCount);
                    }, 2000);
                });
        }
        else
        {
            scope.singleRowSubmit(index + 1, scope.submitSuccessCount, scope.submitFailCount);
        }
    };

    this.validator = function(project) {
        project.errorColumns = {
            title: [],
            regions: {},
        };

        if(!project.title || project.regions.length != scope.regionLabels.length)
        {
            if(!project.title)
            {
                project.errorColumns.title.push('Please enter ' + scope.container.name + ' name');
            }

            if(project.regions.length != scope.regionLabels.length)
            {
                angular.forEach(scope.regionLabels, function(regionLabel, index) {
                    if(!project.regions[index])
                    {
                        project.errorColumns.regions[index] = true;
                    }
                });
            }
            return false;
        }
        else
        {
            return true;
        }
    };

    this.moveFocusByArrow = function(event, row, column) {
        if(event.keyCode == 38)
        {
            if(row > 0)
            {
                document.getElementById('row'+(row-1)+'column'+column).focus();
            }
        }
        else if(event.keyCode == 40)
        {
            if(row+1 < scope.initProjectRowsCount)
            {
                document.getElementById('row'+(row+1)+'column'+column).focus();
            }
        }
        else if(event.keyCode == 37)
        {
            if(column > 0)
            {
                document.getElementById('row'+row+'column'+(column-1)).focus();
            }
        }
        else if(event.keyCode == 39)
        {
            if(column+1 < scope.columnCount)
            {
                document.getElementById('row'+row+'column'+(column+1)).focus();
            }
        }
    };

    this.selectedFormChanged = function() {
        scope.initProjects(scope.parentId);
        scope.columnCount = scope.selectedForm.fields.length+scope.regionLabels.length+1;
    };

    this.toggleUploadModal = function () {
        $uibModal.open({
                animation: true,
                component: 'uploadExcelModal',
                windowClass: 'top-30-percent',
                resolve: {
                    headerMsg: function(){
                        var msg = 'Upload XLS file';
                        if (scope.container.name) {
                            msg += (' to create/edit ' + scope.container.name + 's');
                        }
                        return msg;
                    },
                    projectId: function(){   return scope.parentId },
                    containerId: function(){ return scope.containerId},
                    formId: function() {     return scope.selectedForm.id},
                }
            })
            .result.then(function(obj){
                    $log.debug("Upload Excel modal close successed.");
                    window.history.back();
                }, function(obj){
                    $log.debug("Upload Excel modal dismissed.");
                });
    };

    this.cancelForm = function() {
        // Not sure what to do, redirect back to pervious page for now
        window.history.back();
    };

    this.onDownloadClick = function() {
        if(scope.isExportingExcel) {
            return;
        }

        scope.isExportingExcel = true;
        var requestPromise = null;
        if(scope.parentId){
            requestPromise = ProjectAdminFactory.exportSubrojectExcel(scope.parentId, scope.containerId, scope.selectedForm.id);
        } else {
            requestPromise = ProjectAdminFactory.exportRootProjectExcel(scope.selectedForm.id);
        }

        requestPromise.then(
            function(response) {
                var blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                var linkElement = document.createElement('a');
                linkElement.setAttribute('href', window.URL.createObjectURL(blob));
                linkElement.setAttribute('download', scope.selectedForm.name + '.xls');
                linkElement.dispatchEvent(new MouseEvent('click', {
                    "view": window,
                    "bubbles": true,
                    "cancelable": false
                }));
            }
        ).catch(function(error){
            $log.error('Export excel on failure.');
        }).finally(function(){
            scope.isExportingExcel = false;
        });
    };

    // Initilize
    (function init() {
        scope.initProjectRowsCount = 20;
        scope.projects = [];
        scope.container = {};
        scope.selectedForm = {};

        scope.parentId = window.init_data.parentId;
        scope.containerId = window.init_data.containerId;
        scope.createdBy = window.init_data.createdBy;
        scope.absUri = window.constants.ABS_URI;
        scope.allSubmitSuccess = true;
        scope.isExportingExcel = false;

        if(!scope.parentId)
        {
            scope.initRootProjectBatchCreating();
        }
        else
        {
            scope.initSubProjectBatchCreating();
        }
    })();
})

.component('filterBar', {
    templateUrl: window.constants.ABS_URI + 'partials/admin/project/filter-bar.html',
    bindings: {
        containerId: '<',
        filters: '<',
        conditions: '=',
        onConditionChange: '&?',
        onRefresh: '&?',
    },
    controller: function ($log, ProjectAdminFactory) {
        var scope = this;

        // Component Live Cycle Triggers
        this.$onInit = function(){
            scope.filters = {};
            scope.regions = [];
            scope.fields = {};
        };

        this.$onChanges = function(newObj){
            if (scope.filters) {
                if (scope.filters.region_labels) {
                    scope.initRegionFilters();
                    scope.regionLabels = scope.filters.region_labels;
                }

                if (scope.filters.fields) {
                    scope.initFieldFilters();
                }
            }
        };

        // Private helpers
        this.initRegionFilters = function(){
            scope.conditions.regions = [];
            scope.updateRegions(-1);
        };

        this.initFieldFilters = function(){
            scope.conditions.fields = {};
        };

        this.cleanSubRegions = function(triggerIndex) {
            scope.regions = scope.regions.slice(0, triggerIndex+1);
            if (triggerIndex > 0) {
                scope.conditions.regions = scope.conditions.regions.slice(0, triggerIndex);
            } else {
                scope.conditions.regions = [];
            }
        };

        this.updateRegions = function(triggerIndex) {
            // Initial root regions list, not going to trigger by user
            if (triggerIndex < 0) {
                ProjectAdminFactory.getRootRegions()
                    .then(function(response){
                        if (response.data) {
                            scope.regions[0] = response.data;
                        }
                    })
                    .catch(function(response){
                        $log.warning('getRootRegions XHR fail');
                        scope.regions = [];
                    });
                return;
            }

            var triggerRegionId = scope.conditions.regions[triggerIndex];

            if (!triggerRegionId) {
                scope.cleanSubRegions(triggerIndex);
            } else {
                ProjectAdminFactory.getRegions(triggerRegionId)
                    .then(function(response){
                        if (response.data) {
                            scope.regions[triggerIndex+1] = response.data.subregions;
                        }
                    })
                    .catch(function(response){
                        $log.warning('getRegions XHR fail');
                        delete scope.regions[triggerIndex+1];
                    });
            }
        };

        // Triggers for view
        this.regionChanged = function(triggerIndex) {
            scope.updateRegions(triggerIndex);
            scope.onConditionChange();
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
            scope.onConditionChange();
        };
    },
})

.component('paginationBar', {
    templateUrl: window.constants.ABS_URI + 'partials/admin/project/pagination-bar.html',
    bindings: {
        itemsCount: '<',
        itemsPerPage: '<',
        forms: '<',
        currentPage: '=',
        conditions: '=',
        mode: '=',
        onConditionChange: '&',
    },
    controller: function($timeout) {
        var scope = this;
        var scopeConfig = {
            paginationsLimit: 5,
        };

        // Component Live Cycle Triggers
        this.$onInit = function(){
            // Init scope variables
            scope.currentPage = 0;
        };

        this.$onChanges = function(newObj) {
            if (scope.itemsCount) {
                scope.updatePaginations();
            }
        };

        // Private helpers
        this.getLastPage = function(){
            var itemsCount = parseInt(scope.itemsCount);
            var itemsPerPage = parseInt(scope.itemsPerPage);

            if (itemsCount == 0) {
                return 0;
            }else if (itemsCount % itemsPerPage == 0) {
                return itemsCount / itemsPerPage - 1;
            } else {
                return Math.floor(itemsCount / itemsPerPage);
            }
        };

        this.updatePaginations = function(){
            var paginationsLimit = scopeConfig.paginationsLimit; // Let you know where it from...
            var lastPage = scope.getLastPage();

            scope.paginations = [];

            if (lastPage < paginationsLimit-1) {
                // No enough pages to dissembler
                for (var i=0; i<=lastPage; i++) {
                    scope.paginations.push(i);
                }
            } else if (scope.currentPage < paginationsLimit ) {
                // Current page in left
                for (var i=0; i<paginationsLimit; i++) {
                    scope.paginations.push(i);
                }
            } else if ((lastPage - scope.currentPage) < paginationsLimit) {
                // Current page in right
                for (var i=paginationsLimit-1; i>=0; i--) {
                    scope.paginations.push(lastPage-i);
                }
            } else {
                // Current page in middle
                var offset = Math.floor(paginationsLimit/2);
                for (var i=0; i<paginationsLimit; i++) {
                    scope.paginations.push(scope.currentPage + (i-offset));
                }
            }
        };

        // Triggers for view
        this.pageBegin = function(){
            return parseInt(scope.currentPage) * parseInt(scope.itemsPerPage) + 1;
        };

        this.pageEnd = function(){
            var endNo = (parseInt(scope.currentPage) + 1) * parseInt(scope.itemsPerPage);
            var itemsCount = parseInt(scope.itemsCount) || 0;

            return (itemsCount - endNo >= 0) ? endNo : itemsCount;
        };

        this.goToPage = function(pageNo) {
            var newPageNo;
            var lastPage = scope.getLastPage();

            if (pageNo < 0) {
                newPageNo = 0;
            } else if (pageNo > lastPage){
                newPageNo = scope.currentPage;
            } else {
                newPageNo = Math.floor(pageNo);
            }

            if (newPageNo == scope.currentPage) {
                return;
            }

            scope.currentPage = newPageNo;
            // Workaround for scope binding delay...
            $timeout(function(){
                scope.updatePaginations();
                scope.onConditionChange();
            }, 30);
        };
    },
})

.component('adminProjectList', {
    templateUrl: window.constants.ABS_URI + 'partials/admin/project/project-list.html',
    bindings: {
        containerName: '@',
        mode: '<',
        offset: '<',
        regionLabels: '<',
        form: '<?',
        formFields: '<',
        projects: '<',
        onRefresh: '&?',
        showScrollTool: '@'
    },
    controller: function($log, $uibModal, ProjectAdminFactory) {
        var scope = this;
        this.$onInit = function(){
            scope.absUri = window.constants.ABS_URI;
        };
        this.$onChanges = function(newObj){
          if('formFields' in newObj && newObj.formFields.currentValue)
          {
            var scrollToolElements = angular.element(document.getElementsByClassName("scroll-tools"));
            angular.forEach(scrollToolElements, function(element){
                element.hidden=false;
            });
          }
        };

        this.updateProject = function(project){
            var formFieldValues = {};

            angular.forEach(project.values, function(field, key) {
                formFieldValues[key] = field.value;
            });

            var formId = scope.form ? scope.form : null;

            ProjectAdminFactory.updateProjectValues(project, formId, formFieldValues)
                .then(function(response) {
                    project.mode = 'show';
                    $log.debug('Update project values success');
                })
                .catch(function(response) {
                    $log.debug('Update project values fail: '+response);
                    project.errors = response.data;
                });
        };

        this.confirmDelete = function(projectId) {
            var modal = $uibModal.open({
                animation: true,
                component: 'confirmModal',
                size: 'sm',
                windowClass: 'top-30-percent',
                resolve: {
                    headerMsg: function(){
                        return 'Delete this ' + scope.containerName + '?';
                    },
                },
            })
            .result.then(function(obj){
                ProjectAdminFactory.deleteProject(projectId)
                .then(function(response){
                    scope.onRefresh();
                })
                .catch(function(error){
                    $log.warning('deleteProject XHR fail');
                });
            }, function(obj){
                $log.debug('deleteProject canceled');
            });
        };

        this.scroll = function(key){
          var hierarchy = angular.element(document.getElementsByTagName("body"))[0];
          var vertical = angular.element(document.getElementsByClassName("admin-project-list"))[0];
          switch (key) {
            case 'up':
                hierarchy.scrollTop -= 200;
                break;
            case 'down':
                hierarchy.scrollTop += 200;
                break;
            case 'left':
                vertical.scrollLeft -= 200;
                break;
            case 'right':
                vertical.scrollLeft += 200;
                break;
          }
        }
    },
})

.component('basicInfoShow', {
    templateUrl: window.constants.ABS_URI + 'partials/admin/project/basic-info-show.html',
    bindings: {
        projectId: '<',
        basicInfo: '<'
    },
    controller: function() {
        var scope = this;
        this.$onInit = function() {
            scope.absUri = window.constants.ABS_URI;
        };
    }
})

.component('formShow', {
    templateUrl: window.constants.ABS_URI + 'partials/admin/project/form-show.html',
    bindings: {
        projectId: '<',
        forms: '<',
    },
    controller: function(HelperFactory, $log) {
        var scope = this;
        this.$onInit = function() {
            scope.absUri = window.constants.ABS_URI;
        };

        this.$onChanges = function (newObj) {
          if(!angular.isUndefined(scope.forms) && scope.forms.length > 0) {
                angular.forEach(scope.forms, function(form, formIndex) {
                  angular.forEach(form.values, function(formValue,valueIndex) {
                    if(formValue.hasOwnProperty('field_template_key') && formValue.field_template_key=="gps_tracker"){
                        try {
                            this[valueIndex].value.start_at =  HelperFactory.datetime_converter(formValue.value.start_at);
                            this[valueIndex].value.end_at =  HelperFactory.datetime_converter(formValue.value.end_at);
                        }
                        catch(err) {
                            $log.debug('datetime convert failed.', err);
                        }
                    }

                  }, this[formIndex].values);
                }, scope.forms);
           }
        };
    }
})

.component('confirmModal', {
    template: '\
        <div class="modal-header"> \
            <h3 class="modal-title" id="modal-title"> \
                <% $ctrl.resolve.headerMsg %> \
            </h3> \
        </div> \
        <div class="modal-body" ng-if="$ctrl.resolve.bodyMsg"> \
            <% $ctrl.resolve.bodyMsg %> \
        </div> \
        <div class="modal-footer" style="text-align: center;"> \
            <button class="btn btn-primary" style="width: 45%;" type="button" ng-click="$ctrl.close()">OK</button> \
            <button class="btn btn-warning" style="width: 45%;" type="button" ng-click="$ctrl.dismiss()">Cancel</button> \
        </div> \
     ',
     bindings: {
        resolve: '<',
        close: '&',
        dismiss: '&',
     },
})

.component('uploadExcelModal', {
    templateUrl: window.constants.ABS_URI + 'partials/admin/project/upload-excel-modal.html',
    bindings:{
        resolve: '<',
        projectId: '<?',
        containerId: '<?',
        formId: '<',
        close: '&',
        dismiss: '&',
    },
    controller: function($q, $log, Upload, ProjectAdminFactory){
        var scope = this;
        this.$onInit = function(){
            scope.projectId = scope.resolve.projectId;
            scope.containerId = scope.resolve.containerId;
            scope.formId = scope.resolve.formId;
        };

        this.uploadExcel = function(){
            scope.canceller = $q.defer();
            $log.debug(scope.excelFile);
            if(scope.excelFile)
            {
                $log.debug('Do upload excel');
                ProjectAdminFactory.importExcel(scope.excelFile, scope.canceller)
                    .then(
                        function(response) {
                            scope.responseMessages = [];
                            scope.responseMessages.push({
                                content: response.data.createdProjectCount + ' ' + response.data.containerName + ' created.',
                                color: 'green'
                            });
                            scope.responseMessages.push({
                                content: response.data.updatedProjectCount + ' ' + response.data.containerName + ' updated.',
                                color: 'green'
                            });
                            if(response.data.duplicateTitles)
                            {
                                scope.responseMessages.push({
                                    content: response.data.containerName + ' titles: ' + response.data.duplicateTitles + ' has been taken.',
                                    color: 'red'
                                });
                            }
                            scope.excelUploaded = true;
                        },
                        function(error) {
                            scope.responseMessages = [
                                {
                                    content: 'Upload template failed!',
                                    color: 'red'
                                }
                            ];
                        },
                        function(progress) {
                            scope.excelUploaded = false;
                            scope.responseMessages = [
                                {
                                    content: 'Uploading excel, please wait...'
                                }
                            ];
                        }
                    );
            }
        };

        this.exit = function() {
            scope.dismiss();
            if(scope.excelUploaded)
            {
                window.history.back();
            }
            else
            {
                scope.canceller.resolve();
            }
        };
    }
})

.component('dynamicField', {
    template: '<div ng-class="$ctrl.wrapperClass" ng-include="$ctrl.templateUrl">',
    bindings: {
        'wrapperClass': '@',
        'controlClass': '@',
        'templateKey': '<',
        'options': '<',
        'value': '=',
        'mode': '<?',
        'format': '<?',
        'onChange': '&?',
        'formula': '<',
    },
    controller: function($scope, $compile, $element){
        var scope = this;

        this.$onInit = function(){
            if (!scope.format) {
                scope.format = 'dd LLLL, yyyy';
            }
        };

        this.$onChanges = function(newObj) {
        };

        this.$doCheck = function() {
            if (scope.mode == 'edit') {
                scope.templateUrl = window.constants.ABS_URI + 'partials/fields/edit/' + scope.templateKey + '.html';
            } else {
                scope.templateUrl = window.constants.ABS_URI + 'partials/fields/show/' + scope.templateKey + '.html';
            }
            // Overwrite init model for 'date' template
            if (typeof scope.value == 'string' && scope.templateKey == 'date') {
                scope.value = new Date(scope.value);
            }
        };
    }
})

.component('attachUploader', {
    templateUrl: window.constants.ABS_URI + 'partials/form_control/attach_uploader.html',
    bindings: {
        wrapperClass: '@',
        controlClass: '@',
        attachIds: '=',
        attaches: '=?', //optional, for detail info
        onSuccess: '&',
    },
    controller: function($timeout, $log, Upload, ProjectAdminFactory){
        var scope = this;

        this.$onInit = function(){
            scope.resetUploader();
        };

        this.resetUploader = function(){
            scope.errMsg = false;
            scope.progressPercent = false;
            scope.attach = null;
        };

        this.onFileChange = function(){
            if (scope.attach) {
                scope.progressClass = "progress-striped active";
                if (scope.attach.size < 32000000) {
                    ProjectAdminFactory.uploadAttachment(scope.attach)
                    .then(
                        function(response){
                            // success
                            $log.debug("File upload success");

                            if (response.data.length > 0) {
                                scope.attaches = response.data;
                                scope.attachIds = response.data.map(function(attach){
                                    return attach.id;
                                });
                            }

                            scope.progressPercent = 100;
                            scope.errMsg = "";
                            $timeout(function(){
                                scope.progressType = 'success';
                                scope.progressClass = "";
                                scope.progressPercent = 0;

                                scope.onSuccess();
                            }, 1500);
                        },
                        function(err){
                            // error
                            $log.error(err);

                            scope.progressClass = "";
                            scope.progressType = 'danger';

                            scope.attach = {};

                            try{
                                scope.errMsg = err.data.file[0];
                            }catch(exp){
                                scope.errMsg = "File upload failed.";
                            }
                        },
                        function(progress){
                            // in progress
                            $log.debug("File upload in progress");
                            scope.errMsg = "";
                            scope.progressType = 'info';
                            scope.progressPercent = Math.floor(progress.loaded / progress.total);
                        }
                    );
                }
                else{
                    scope.attach = {};
                    scope.errMsg = "File size is over the limit.";
                    $log.error("File size is over the limit.");
                }
            } else {
                scope.progressPercent = false;
                scope.progressClass = "progress";
            }
        };
    }
})

.component('multiAttachUploader', {
    templateUrl: window.constants.ABS_URI + 'partials/form_control/multi-attach_uploader.html',
    bindings: {
        attachIds: '=', // Must be an array
        attaches: '<?' // For edit mode initial, will overwirte attachIds by the ids in it
    },
    controller: function($timeout, $log, $uibModal, ProjectAdminFactory){
        var scope = this;
        this.$onInit = function(){
            scope.newAttaches = [];
            scope.newAttachIds = [];
        };

        this.$onChanges = function(){
            if (scope.attaches) {
                var attachIds = [];

                angular.forEach(scope.attaches, function(value, key) {
                    this.push(value.id);
                }, attachIds);
                scope.attachIds = attachIds;
            } else {
                scope.attachIds = [];
                scope.attaches = [];
            }
            scope.newAttaches = [];
            scope.newAttachIds = [];
        };

        this.onUploadFinished = function(){
            $log.debug('Upload finished: ' + scope.newAttachIds);
            scope.attaches = scope.attaches.concat(scope.newAttaches);
            scope.attachIds = scope.attachIds.concat(scope.newAttachIds);
        };

        this.deleteAttach = function(index){
            var modal = $uibModal.open({
                animation: true,
                component: 'confirmModal',
                windowClass: 'top-30-percent',
                resolve: {
                    headerMsg: function(){
                        return 'Delete file?';
                    },
                    bodyMsg: function(){
                        return 'Confirm delete file ' + scope.attaches[index].name + '?';
                    },
                },
            })
            .result.then(function(obj){
                $log.debug('Deleting file '+ scope.attaches[index].name +'...');
                if (index > -1) {
                    scope.attaches.splice(index, 1);
                    scope.attachIds.splice(index, 1);
                }
            }, function(obj){
                $log.debug('delete file '+ scope.attaches[index].name +' canceled');
            });

        };
    }
})

.component('formImagesEdit', {
    templateUrl: window.constants.ABS_URI + 'partials/admin/project/form-images-edit.html',
    bindings: {
        mediaGroups: '=',
    },
    controller: function(ProjectAdminFactory) {
        var scope = this;

        this.deleteImage = function(index, mediaGroup) {
            mediaGroup.items.splice(index, 1);
            if(mediaGroup.items.length == 0)
            {
                scope.mediaGroups.splice(scope.mediaGroups.indexOf(mediaGroup), 1);
            }
        };

        this.rotateImage = function(item) {
            ProjectAdminFactory.rotateImage(item.attachment_id)
                .then(function(response) {
                    item.attachment_path = window.constants.ABS_URI + 'file/' + item.attachment_id + '?' + new Date().getTime();
                })
                .catch(function(response) {

                });
        };
    }
})

.component('batchInputs', {
    templateUrl: window.constants.ABS_URI + 'partials/admin/project/batch-inputs.html',
    bindings: {
        regionLabels: '<',
        selectedForm: '<',
        projects: '=',
        createdBy: '<',
        containerName: '<'
    },
    controller: function(ProjectAdminFactory) {
        var scope = this;

        this.autoFillSupervisor = function(project) {
            if(project.title || project.regions.length > 0 || project.values)
            {
                project.supervisor = scope.createdBy;
            }
            else
            {
                project.supervisor = null;
            }
        };

        this.regionChanged = function(project, regionIndex) {
            var regionLevel = scope.regionLabels.length;

            if(regionIndex+1 != regionLevel)
            {
                project.regions.splice(regionIndex+1, regionLevel-regionIndex+1);
                project.regionOptions.splice(regionIndex+1, regionLevel-regionIndex+1);

                if(project.regions[regionIndex])
                {
                    ProjectAdminFactory.getRegions(project.regions[regionIndex])
                        .then(function(response) {
                            project.regionOptions[regionIndex+1] = response.data.subregions;
                        })
                        .catch(function(response) {
                            //
                        });
                }
            }
            scope.autoFillSupervisor(project);
        };
    }
});
