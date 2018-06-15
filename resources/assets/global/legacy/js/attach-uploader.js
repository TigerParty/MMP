//-- Need constants global variable under winodw
//-- window.constants.CSRF_TOKEN
//-- window.constants

var AttachUploader = angular.module('AttachUploader', ['ngFileUpload'])
.config(function($interpolateProvider, $httpProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
})

.service('FileService', function(Upload, $timeout) {
    var that = this;
    this.files = [];
    this.logo = [];

    this.NewFile = function(name, isLogo) {
        var file = {
            index: that.files.length,
            name: name,
            upload_bar_class: "",
            progress: 0,
            progressStatus: "",
            uploadedData: false
        };

       if(isLogo){
         that.logo.splice(0, 1);
         that.logo.push(file);
       }else{
         that.files.push(file);
       }

       return file;
    };

    this.removeFile = function(file) {
        that.files.splice(file.index, 1);
    };

    this.removeLogo = function() {
        that.logo.splice(0, 1);
    };

    this.removeDuplicateFile = function(new_file) {
        var isDuplicate = false;
        angular.forEach(that.files, function(file, index) {
            if(new_file.index != index && file.uploadedData.id == new_file.uploadedData.id) {
                isDuplicate = true;
            }
        })

        if (isDuplicate) {
            that.removeFile(new_file);
        }
    };

    this.initFiles = function(files) {
        that.files = files;
    };

    this.getFiles = function() {
        return that.files;
    };

    this.getLogo = function() {
      return that.logo;
    };
})

.controller('fileController', function ($scope, FileService) {
    this.scope = $scope;
    $scope.removeFile = function(file) {
        FileService.removeFile(file);
    };
    $scope.removeLogo = function() {
        FileService.removeLogo();
    };

})

.directive('logoShow', function($rootScope, FileService) {
    return{
        controller: 'fileController',
        scope: {
          logoId: '=file'
        },
        link: function(scope, element, attrs) {
            if(!angular.isUndefined(scope.logoId) && scope.logoId!==null){
              scope.file = FileService.NewFile('logoImg', true);
              var logo_path = window.constants.ABS_URI + "file/" + scope.logoId;
              scope.file.uploadedData = {id: scope.logoId,
                                                  asset_path: logo_path
                                                  };
            }
            scope.new_logo = FileService.getLogo();
        },
        templateUrl: function(element, attrs) {
            var tempUrl = attrs.templateUrl || "partials/logo-show.html";
            return window.constants.ABS_URI + tempUrl
        }
    }
})

.directive('filegroup', function($rootScope, FileService) {
    return {
        controller: 'fileController',
        scope: {
            files: '='
        },
        link: function(scope, element, attrs) {
            scope.new_files = FileService.getFiles();
            scope.cancelUpload = function(){
                $rootScope.cancelUpload();
            };
        },
        templateUrl: function(element, attrs) {
            var tempUrl = attrs.templateUrl || "partials/filegroup.html";
            return window.constants.ABS_URI + tempUrl
        }
    }
})

.directive('fileuploader', function($rootScope, Upload, FileService, $timeout){
    return {
        templateUrl: function(element, attrs) {
            var tempUrl = attrs.templateUrl || "partials/filegroup.html";
            return window.constants.ABS_URI + tempUrl },
        scope: true,
        link: function(scope, element, attrs){
            scope.isLogo = false;
            if(!angular.isUndefined(attrs.type)){ scope.isLogo=true;}

            scope.uploadFile = function(file) {
                if( file && !file.$error) {
                    scope.file = FileService.NewFile(file.name, scope.isLogo);
                    if(file.size > 32000000)
                    {
                        scope.file.progress = 100;
                        scope.file.upload_bar_class = "progress-bar-danger";
                        scope.file.progressStatus = "File too large";
                        $timeout(function(){
                            FileService.removeFile(scope.file);
                        }, 3000);
                    }
                    else
                    {
                        scope.Uploader = Upload.upload({
                            url: window.constants.ABS_URI + 'file',
                            file: file,
                            method: 'POST',
                            fields: {
                                '_token': window.constants.CSRF_TOKEN
                            }
                        }).then(function(resp) {
                            // success
                            $rootScope.upload_in_progress = false;
                            scope.file.upload_bar_class = "progress-bar-success";
                            scope.file.progressStatus = "Upload Completed";
                            scope.file.uploadedData = resp.data;
                            FileService.removeDuplicateFile(scope.file);
                            $timeout(function(){ scope.progress = 0; }, 2000);
                        }, function(resp){
                            // eror
                            $rootScope.upload_in_progress = false;
                            scope.file.upload_bar_class = "progress-bar-danger";
                            if(status == 400){
                                scope.file.progressStatus = "Error";
                            }else if(status == 422){
                                scope.file.progressStatus = "File too large";
                            }else{
                                scope.file.progressStatus = "Canceled";
                            }
                            $timeout(function(){
                                FileService.removeFile(scope.file);
                            }, 3000);
                        }, function(evt) {
                            //progress
                            $rootScope.upload_in_progress = true;
                            var percentage = parseInt(100.0 * evt.loaded / evt.total);
                            scope.file.upload_bar_class = "progress-bar-info";
                            scope.file.progress = percentage;
                            scope.file.progressStatus = percentage + "%";
                        });
                    }
                }
            }

            $rootScope.cancelUpload = function(){
                scope.Uploader.abort();
                $timeout(function(){
                    scope.file.progress = 0;
                    scope.file.upload_bar_class = "progress-bar-danger";
                    scope.file.progressStatus = "Canceled";

                }, 2000)
            };
        } //-- EOF link
    }; //-- EOF directive return obj
});
