var DyncFormApp = angular.module('DyncFormApp', ['ui.bootstrap']);

DyncFormApp.config(function($interpolateProvider, $httpProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

DyncFormApp.service('DyncFormService', function ($http) {
    this.getNewField = function(index) {
        return {
            id : 'new_' + index,
            name : "",
            type : "",
            default_value : "",
            edit_level : "",
            view_level : "",
            options : "[]",
            options_to_show: "",
            isShowOptions : false,
            show_if : {'field_id': null,'options': []}
        };
    };

    this.getNewDyncForm = function() {
        return {
            name : "",
            fields : [this.getNewField()]
        };
    };

    this.convertJsonToCsv = function(items){
        if(typeof(items) == 'string'){
            try {
                items = JSON.parse(items);
            } catch(err) {
                console.log(err);
                items = [];
            }
        }
        var csv = "";
        for(var key in items){
            if(key>0){
                csv += (","+items[key].toString());
            }else{
                csv += items[key].toString();
            }
        }
        return csv;
    };
});

DyncFormApp.controller('IndexCtrl', function ($scope, $uibModal, $log) {
    $scope.dynamic_forms = window.init_data.dynamic_forms;

    $scope.openConfirm = function(deleteId) {
        $uibModal.open({
          animation: true,
          templateUrl: window.constants.ABS_URI + 'partials/confirm.html',
          controller: 'ConfirmCtrl',
          resolve: {
            deleteId: function () {
              return deleteId;
            }
          }
        });
    };

});

DyncFormApp.controller('ConfirmCtrl', function ($scope, $uibModalInstance, deleteId) {
    $scope.delete_url = '/admin/dync_form/' + deleteId + '/delete';
    $scope.namespace = 'form';
    $scope.delete = function () {
        $uibModalInstance.close();
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
});

DyncFormApp.controller('CreateCtrl', function ($scope, $log, DyncFormService) {
    $scope.addNewDyncField = function() {
        $scope.dynamic_form.fields.push(DyncFormService.getNewField($scope.dynamic_form.fields.length));
    };

    $scope.deleteDyncField = function(f_idx) {
        $scope.dynamic_form.fields.splice(f_idx, 1);
    };

    $scope.showOptionsByTemplate = function(field) {
        if(field.template != null) {
            switch(field.template.name) {
                case 'DropDownList':
                    try{
                        field.options_to_show = DyncFormService.convertJsonToCsv(field.options);
                        field.isShowOptions = true;
                    }catch(err){
                        console.log("[Error]showOptionsByTemplate: "+err.toString());
                        field.isShowOptions = false;
                    }
                    break;
                case 'RadioButton':
                    if(field.options == undefined)
                    {
                        field.options = [];
                    }
                    try{
                        field.options_to_show = DyncFormService.convertJsonToCsv(field.options);
                        field.isShowOptions = true;
                    }catch(err){
                        console.log("[Error]showOptionsByTemplate: "+err.toString());
                        field.isShowOptions = false;
                    }
                    break;
                case 'RadioButton (Conditional)':
                    if(field.options == undefined)
                    {
                        field.options = [];
                    }
                    try{
                        field.options_to_show = DyncFormService.convertJsonToCsv(field.options);
                        field.isShowOptions = true;
                    }catch(err){
                        console.log("[Error]showOptionsByTemplate: "+err.toString());
                        field.isShowOptions = false;
                    }
                    break;
                case 'CheckBoxGroup':
                    try {
                        field.options_to_show = DyncFormService.convertJsonToCsv(field.options);
                        field.isShowOptions = true;
                    } catch (err) {
                        console.log("[Error]showOptionsByTemplate: " + err.toString());
                        field.isShowOptions = false;
                    }
                    break;
                default:
                    field.isShowOptions = false;
                    break;
            }
        }
    };

    $scope.initShowIf = function(field) {
        if(field.show_if == undefined){
            field.show_if = {'field_id': null,'options': []};
        } else {
            var show_if = JSON.parse(field.show_if);
            field.show_if = show_if;
            field.show_if.field_id = Object.keys(show_if)[0];
            field.show_if.equals = show_if[Object.keys(show_if)[0]][0];
            if(field.show_if.field_id){
                $scope.getSourceFieldOptions(field.show_if);
                field.showing_if_panel = true;
            }
        }
    };

    $scope.showIfChange = function(field) {
        if(field.showing_if_panel == false)
        {
            field.show_if.field_id = null;
            field.show_if.equals = null;
        }
    };

    $scope.unDisabled = function() {
        $scope.isDisabled = false;
    };

    $scope.getSourceFieldOptions = function(objShowIf) {
        var source_field = $scope.dynamic_form.fields.filter(function(item) {
            return item.id == objShowIf.field_id;
        });
        if(source_field.length > 0){
            objShowIf.options = source_field[0].options;
            if(objShowIf.options.indexOf(objShowIf.equals) < 0) {
                objShowIf.equals = null;
            }
        }
    };

    $scope.generateOptions = function(radiobtnField) {
        radiobtnField.options = radiobtnField.options_to_show.split(',');

        angular.forEach($scope.dynamic_form.fields, function(field, index) {
            if(field.show_if.field_id == radiobtnField.id){
                field.show_if.options = radiobtnField.options;
            }
        });
    };

    $scope.switchUp = function (currentIndex) {
        if (currentIndex > 0) {
            var tempField = $scope.dynamic_form.fields[currentIndex - 1];
            $scope.dynamic_form.fields[currentIndex - 1] = $scope.dynamic_form.fields[currentIndex];
            $scope.dynamic_form.fields[currentIndex] = tempField;
        }
    };

    $scope.switchDown = function (currentIndex) {
        if (currentIndex < $scope.dynamic_form.fields.length - 1) {
            var tempField = $scope.dynamic_form.fields[currentIndex + 1];
            $scope.dynamic_form.fields[currentIndex + 1] = $scope.dynamic_form.fields[currentIndex];
            $scope.dynamic_form.fields[currentIndex] = tempField;
        }
    };

    (function init() {
        $scope.isDisabled = true;

        $scope.dynamic_form = window.init_data.dynamic_form;

        $scope.field_templates = window.init_data.field_templates;
        $scope.pms_levels = window.init_data.pms_levels;

        angular.forEach($scope.dynamic_form.fields, function(field, index) {
            $scope.showOptionsByTemplate(field);
            $scope.initShowIf(field);
        });

        if(!$scope.dynamic_form.fields)
        {
            $scope.dynamic_form.fields = [];
            $scope.addNewDyncField();
        }
    })();
});

DyncFormApp.controller('EditCtrl', function ($scope, $log, DyncFormService) {
    $scope.addNewDyncField = function() {
        $scope.dynamic_form.fields.push(DyncFormService.getNewField($scope.dynamic_form.fields.length));
    };

    $scope.deleteDyncField = function(f_idx) {
        $scope.dynamic_form.fields.splice(f_idx, 1);
    };

    $scope.showOptionsByTemplate = function(field) {
        if(field.template != null) {
            switch(field.template.name) {
                case 'DropDownList':
                    try{
                        field.options_to_show = DyncFormService.convertJsonToCsv(field.options);
                        field.isShowOptions = true;
                    }catch(err){
                        console.log("[Error]showOptionsByTemplate: "+err.toString());
                        field.isShowOptions = false;
                    }
                    break;
                case 'RadioButton':
                    if(field.options == undefined)
                    {
                        field.options = [];
                    }
                    try{
                        field.options_to_show = DyncFormService.convertJsonToCsv(field.options);
                        field.isShowOptions = true;
                    }catch(err){
                        console.log("[Error]showOptionsByTemplate: "+err.toString());
                        field.isShowOptions = false;
                    }
                    break;
                case 'RadioButton (Conditional)':
                    if(field.options == undefined)
                    {
                        field.options = [];
                    }
                    try{
                        field.options_to_show = DyncFormService.convertJsonToCsv(field.options);
                        field.isShowOptions = true;
                    }catch(err){
                        console.log("[Error]showOptionsByTemplate: "+err.toString());
                        field.isShowOptions = false;
                    }
                    break;
                case 'CheckBoxGroup':
                    try {
                        field.options_to_show = DyncFormService.convertJsonToCsv(field.options);
                        field.isShowOptions = true;
                    } catch (err) {
                        console.log("[Error]showOptionsByTemplate: " + err.toString());
                        field.isShowOptions = false;
                    }
                    break;
                default:
                    field.isShowOptions = false;
                    break;
            }
        }
    };

    $scope.initShowIf = function(field) {
        if(field.show_if == undefined){
            field.show_if = {'field_id': null,'options': []};
        } else {
            var show_if = (field.show_if) ? field.show_if : [];
            field.show_if = show_if;
            field.show_if.field_id = Object.keys(show_if)[0];
            field.show_if.equals = show_if[Object.keys(show_if)[0]][0];
            if(field.show_if.field_id){
                $scope.getSourceFieldOptions(field.show_if);
                field.showing_if_panel = true;
            }
        }
    };

    $scope.showIfChange = function(field) {
        if(field.showing_if_panel == false)
        {
            field.show_if.field_id = null;
            field.show_if.equals = null;
        }
    };

    $scope.initDynamicForm = function () {
        if ($scope.dynamic_form.is_photo_required == 1) {
            $scope.dynamic_form.is_photo_required = true;
        } else {
            $scope.dynamic_form.is_photo_required = false;
        }
    };

    $scope.initFieldRequired = function (field) {
        if (field.is_required == 1) {
            field.is_required = true;
        } else {
            field.is_required = false;
        }
    };

    $scope.unDisabled = function() {
        $scope.isDisabled = false;
    };

    $scope.getSourceFieldOptions = function(objShowIf) {
        var source_field = $scope.dynamic_form.fields.filter(function(item) {
            return item.id == objShowIf.field_id;
        });
        if(source_field.length > 0){
            objShowIf.options = source_field[0].options;
            if(objShowIf.options.indexOf(objShowIf.equals) < 0) {
                objShowIf.equals = null;
            }
        }
    };

    $scope.generateOptions = function(radiobtnField) {
        radiobtnField.options = radiobtnField.options_to_show.split(',');

        angular.forEach($scope.dynamic_form.fields, function(field, index) {
            if(field.show_if.field_id == radiobtnField.id){
                field.show_if.options = radiobtnField.options;
            }
        });
    };

    $scope.switchUp = function (currentIndex) {
        if (currentIndex > 0) {
            var tempField = $scope.dynamic_form.fields[currentIndex - 1];
            $scope.dynamic_form.fields[currentIndex - 1] = $scope.dynamic_form.fields[currentIndex];
            $scope.dynamic_form.fields[currentIndex] = tempField;
        }
    };

    $scope.switchDown = function (currentIndex) {
        if (currentIndex < $scope.dynamic_form.fields.length - 1) {
            var tempField = $scope.dynamic_form.fields[currentIndex + 1];
            $scope.dynamic_form.fields[currentIndex + 1] = $scope.dynamic_form.fields[currentIndex];
            $scope.dynamic_form.fields[currentIndex] = tempField;
        }
    };

    (function init() {
        $scope.isDisabled = true;

        $scope.dynamic_form = window.init_data.dynamic_form;
        $scope.field_templates = window.init_data.field_templates;
        $scope.pms_levels = window.init_data.pms_levels;

        $scope.initDynamicForm();

        angular.forEach($scope.dynamic_form.fields, function(field, index) {
            $scope.showOptionsByTemplate(field);
            $scope.initShowIf(field);
            $scope.initFieldRequired(field);
        });
    })();
});

