angular.module("NotificationApp", ['ui.bootstrap'])

.config(function($interpolateProvider, $logProvider, $httpProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $logProvider.debugEnabled(false);
    $httpProvider.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
})

.filter('regionTitleize', function() {
    return function(input) {
        input = input || '';
        return "-- " + input.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();}) + " --";
    };
})

.factory('NotificationFactory', function($http) {
    return {
        getProjects : function() {
            return $http.get(window.constants.ABS_URI + 'admin/notification/api');
        },
        queryProjects : function(conditions, order) {
            return $http.post(window.constants.ABS_URI + 'admin/notification/api', {
                conditions: conditions,
                order: order,
                _token : window.constants.CSRF_TOKEN,
            });
        },
        getRootRegions : function() {
            return $http.get(window.constants.ABS_URI + 'region/api');
        },
        getRegions : function(regionId) {
            return $http.get(window.constants.ABS_URI + 'region/' + regionId + '/api');
        },
        getProjectEmails : function(projectId) {
            return $http.get(window.constants.ABS_URI + 'admin/notification/email/project/' + projectId + '/api');
        },
        saveProjectEmails : function(projectId, emails) {
            return $http.post(window.constants.ABS_URI + 'admin/notification/email/project/' + projectId, {
                emails: emails,
                _token: window.constants.CSRF_TOKEN
            });
        },
        getProjectSMSes : function(projectId) {
            return $http.get(window.constants.ABS_URI + 'admin/notification/sms/project/' + projectId + '/api');
        },
        saveProjectSMSes : function(projectId, SMSes) {
            return $http.post(window.constants.ABS_URI + 'admin/notification/sms/project/' + projectId, {
                SMSes: SMSes,
                _token: window.constants.CSRF_TOKEN
            });
        },
    };
})

.factory("NotificationInstance", function(){
    return {
        frequencies: function(){
            return [
                {
                    'title': 'Monthly',
                    'value': 'monthly'
                },
                {
                    'title': 'Weekly',
                    'value': 'weekly'
                },
                {
                    'title': 'Daily',
                    'value': 'daily'
                },
                {
                    'title': 'By Update',
                    'value': 'by_update'
                },
            ];
        },
        emailInstance: function(email, notificationId){
            return {
                id:          email ? email.id : null,
                receiver:    email ? email.receiver : "",
                email:       email ? email.email : "",
                schedule:    email ? email.schedule : "by_update",
                notify_id:   email ? email.notify_id : notificationId,
                editable:    email ? false : true,
                showCheck:   false,
                isValid:     email ? true : false,
                validateFields: {
                    receiver: true,
                    email:    true,
                    schedule: true
                }
            };
        },
        SMSInstance: function(SMS, notificationId){
            return {
                id:           SMS ? SMS.id : null,
                receiver:     SMS ? SMS.receiver : "",
                phone_number: SMS ? SMS.phone_number : "",
                schedule:     SMS ? SMS.schedule : "by_update",
                notify_id:    SMS ? SMS.notify_id : notificationId,
                editable:     SMS ? false : true,
                showCheck:    false,
                isValid:      SMS ? true : false,
                validateFields: {
                    receiver:     true,
                    phone_number: true,
                    schedule:     true
                }
            };
        }
    };
})

.factory("Validator", ['NotificationInstance',
    function(NotificationInstance){
        return {
            checkReceiver: function(receiver){
                return /^[\w\s,.-]{1,255}$/.test(receiver);
            },
            checkEmail: function(email){
                return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
            },
            checkPhoneNumber: function(phoneNumber){
                return /^\+{0,1}\d{1,}$/.test(phoneNumber);
            },
            checkSchedule: function(schedule){
                if(NotificationInstance.frequencies().filter(function(e){ return e.value == schedule; }).length > 0){
                    return true;
                }
                return false;
            }
        };
    }
])

.controller('IndexCtrl', function ($scope, $uibModal, NotificationFactory) {
    scope = this;

    // Configurations
    var paginationsLimit = 5;
    scope.itemPerPage = 10;

    // Init variables
    scope.currentPage = 0;
    scope.lastPage = 0;
    scope.paginations = [];

    scope.regions = [];
    scope.conditions = {
        keyword: '',
        regions: []
    };

    scope.orderBy = "";

    // Private helpers
    scope.updatePageNo = function(itemLength){
        scope.currentPage = 0;

        if (itemLength == 0) {
            scope.lastPage = 0;
        }else if (itemLength % scope.itemPerPage == 0) {
            scope.lastPage = itemLength / scope.itemPerPage - 1;
        } else {
            scope.lastPage = Math.floor(itemLength / scope.itemPerPage);
        }
        scope.updatePaginations();
    };

    scope.updatePaginations = function(){
        scope.paginations = [];
        if (scope.lastPage < paginationsLimit-1) {
            // No enough pages to dissembler
            for (var i=0; i<=scope.lastPage; i++) {
                scope.paginations.push(i);
            }
        } else if (scope.currentPage < paginationsLimit ) {
            // Current page in left
            for (var i=0; i<paginationsLimit; i++) {
                scope.paginations.push(i);
            }
        } else if ((scope.lastPage - scope.currentPage) < paginationsLimit) {
            // Current page in right
            for (var i=paginationsLimit-1; i>=0; i--) {
                scope.paginations.push(scope.lastPage-i);
            }
        } else {
            // Current page in middle
            var offset = Math.floor(paginationsLimit/2);
            for (var i=0; i<paginationsLimit; i++) {
                scope.paginations.push(scope.currentPage + (i-offset));
            }
        }
    };

    scope.cleanSubRegions = function(triggerIndex) {
        scope.regions = scope.regions.slice(0, triggerIndex+1);
        scope.conditions.regions = scope.conditions.regions.slice(0, triggerIndex+1);
    };

    scope.updateRegions = function(triggerIndex) {
        // Initial root regions list, not going to trigger by user
        if (triggerIndex < 0) {
            NotificationFactory.getRootRegions().then(function(response){
                if (response.data) {
                    scope.regions[0] = response.data;
                }
            });
            return;
        }

        var triggerRegionId = scope.conditions.regions[triggerIndex];

        if (!triggerRegionId) {
            scope.cleanSubRegions(triggerIndex);
        } else {
            NotificationFactory.getRegions(triggerRegionId).then(function(response){
                if (response.data) {
                    scope.regions[triggerIndex+1] = response.data.subregions;
                }
            });
        }
    };

    // Public helpers
    scope.goToPage = function(pageNo) {
        if (pageNo < 0) {
            scope.currentPage = 0;
        } else if (pageNo <= scope.lastPage){
            scope.currentPage = Math.floor(pageNo);
        }
        scope.updatePaginations();
    };

    scope.queryProjects = function() {
        var conditionRegionIds = scope.conditions.regions
            .map(function(regionId){
                return parseInt(regionId);
            })
            .filter(function(regionId){
                return regionId ? true : false;
            });
        var conditions = {
            keyword : scope.conditions.keyword,
            regions : conditionRegionIds,
        };
        var order = scope.orderBy;

        NotificationFactory.queryProjects(conditions, order).then(function(response){
            if (response.data) {
                scope.projects = response.data.projects;
                scope.updatePageNo(scope.projects.length);
            }
        });
    };

    scope.regionChanged = function(triggerIndex) {
        scope.updateRegions(triggerIndex);
        scope.queryProjects();
    };

    scope.openEmailModal = function(projectId) {
        NotificationFactory.getProjectEmails(projectId).then(function(response){
            var emails = response.data.emails;
            $uibModal.open({
                animation: true,
                backdrop: 'static',
                component: 'addEmailModal',
                windowClass: 'notificationModal',
                resolve: {
                    items: function(){
                        return emails;
                    },
                    notificationId: function(){
                        return projectId;
                    }
                }
            }).result.then(function(){

            }, function(){

            });
        });
     };

     scope.openSMSModal = function(projectId) {
        NotificationFactory.getProjectSMSes(projectId).then(function(response){
            var SMSes = response.data.smses;
            $uibModal.open({
                animation: true,
                backdrop: 'static',
                component: 'addSMSModal',
                windowClass: 'notificationModal',
                resolve: {
                    items: function(){
                        return SMSes;
                    },
                    notificationId: function(){
                        return projectId;
                    }
                }
            }).result.then(function(){

            }, function(){

            });
        });
     };

    // Initilize
    (function init() {
        NotificationFactory.getProjects().then(function(response){
            if (response.data) {
                scope.projects = response.data.projects;
                scope.updatePageNo(scope.projects.length);
                scope.labels = response.data.region_labels;
                scope.rootRegions = response.data.root_regions;
            }
        });

        scope.updateRegions(-1);
    })();
})
.component('addEmailModal', {
    templateUrl: window.constants.ABS_URI + 'partials/admin/notification/createEmail.html',
    bindings: {
        resolve: '<',
        close: '&',
        dismiss: '&',
    },
    controller: function($timeout, NotificationInstance, NotificationFactory, Validator) {
        var scope = this;

        this.$onInit = function(){
            scope.saveOnSuccess = false;
            scope.frequencies = NotificationInstance.frequencies();

            //-- convert to custom object
            scope.resolve.items.forEach(function(item, index, theArray){
                theArray[index] = NotificationInstance.emailInstance(
                    item,
                    scope.resolve.notificationId
                );
            });
        };

        scope.editItem = function(item){
            item.editable = !item.editable;
        };

        scope.saveItem = function(item){
            if (validateItem(item)){
                item.isValid = true;
                item.showCheck = true;
                item.editable = !item.editable;

                $timeout(function(){
                    item.showCheck = false;
                }, 2000);
            }
            else{
                item.isValid = false;
            }
        };

        scope.deleteItem = function(index){
            scope.resolve.items.splice(index, 1);
        };

        scope.addNewItem = function(){
            scope.resolve.items.push(NotificationInstance.emailInstance(
                null,
                scope.resolve.notificationId
            ));
        };

        scope.saveAllItem = function(){
            NotificationFactory.saveProjectEmails(
                scope.resolve.notificationId,
                scope.resolve.items
            ).then(function(response){
                if(response.status == 200){
                    scope.saveOnSuccess = true;
                }
            }, function(error){
                console.log(error);
            });
        };

        scope.changeTitle = function(value){
            var result = scope.frequencies.filter(function(e){
                return e.value == value;
            });
            if (result.length > 0){
                return result[0].title;
            }
            else {
                return "";
            }
        };

        scope.saveButtonDisabled = function(){
            var result = false;
            for (var i = 0; i < scope.resolve.items.length; i++) {
                if (!scope.resolve.items[i].isValid ||
                    scope.resolve.items[i].editable){
                    result = true;
                    break;
                }
            }

            return result;
        };

        function validateItem(item){
            item.validateFields.receiver = Validator.checkReceiver(item.receiver);
            item.validateFields.email = Validator.checkEmail(item.email);
            item.validateFields.schedule = Validator.checkSchedule(item.schedule);

            if (item.validateFields.receiver &&
                item.validateFields.email &&
                item.validateFields.schedule){
                return true;
            }
            else{
                return false;
            }
        }
    }
})
.component('addSMSModal', {
    templateUrl: window.constants.ABS_URI + 'partials/admin/notification/createSMS.html',
    bindings: {
        resolve: '<',
        close: '&',
        dismiss: '&',
    },
    controller: function($timeout, NotificationInstance, NotificationFactory, Validator) {
        var scope = this;

        this.$onInit = function(){
            scope.saveOnSuccess = false;
            scope.frequencies = NotificationInstance.frequencies();

            //-- convert to custom object
            scope.resolve.items.forEach(function(item, index, theArray){
                theArray[index] = NotificationInstance.SMSInstance(
                    item,
                    scope.resolve.notificationId
                );
            });
        };

        scope.editItem = function(item){
            item.editable = !item.editable;
        };

        scope.saveItem = function(item){
            if (validateItem(item)){
                item.isValid = true;
                item.showCheck = true;
                item.editable = !item.editable;

                $timeout(function(){
                    item.showCheck = false;
                }, 2000);
            }
            else{
                item.isValid = false;
            }
        };

        scope.deleteItem = function(index){
            scope.resolve.items.splice(index, 1);
        };

        scope.addNewItem = function(){
            scope.resolve.items.push(NotificationInstance.SMSInstance(
                null,
                scope.resolve.notificationId
            ));
        };

        scope.saveAllItem = function(){
            NotificationFactory.saveProjectSMSes(
                scope.resolve.notificationId,
                scope.resolve.items
            ).then(function(response){
                if(response.status == 200){
                    scope.saveOnSuccess = true;
                }
            }, function(error){
                console.log(error);
            });
        };

        scope.changeTitle = function(value){
            var result = scope.frequencies.filter(function(e){
                return e.value == value;
            });
            if (result.length > 0){
                return result[0].title;
            }
            else {
                return "";
            }
        };

        scope.saveButtonDisabled = function(){
            var result = false;
            for (var i = 0; i < scope.resolve.items.length; i++) {
                if (!scope.resolve.items[i].isValid ||
                    scope.resolve.items[i].editable){
                    result = true;
                    break;
                }
            }

            return result;
        };

        function validateItem(item){
            item.validateFields.receiver = Validator.checkReceiver(item.receiver);
            item.validateFields.phone_number = Validator.checkPhoneNumber(item.phone_number);
            item.validateFields.schedule = Validator.checkSchedule(item.schedule);

            if (item.validateFields.receiver &&
                item.validateFields.phone_number &&
                item.validateFields.schedule){
                return true;
            }
            else{
                return false;
            }
        }
    }
});

