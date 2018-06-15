var DatePicker = angular.module('DatePicker', ['ui.bootstrap', 'ngAnimate'])
.config(function($interpolateProvider, $httpProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $httpProvider.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
});

DatePicker.controller('DatePickCtrl', function ($scope, $filter) {
	var scope = $scope;

	scope.openDatePicker = function ($event, $index) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.DatePickerOpened = true;
    };

    scope.$watch('date', function(newValue, oldValue) {
        if(newValue && angular.isDate(newValue)) {
            scope.sql_date = $filter('date')(new Date(newValue), 'yyyy-MM-dd 00:00:00');
        }
    });

    function initDisplayDateFormat () {
        if(scope.date) {
            scope.date = new Date(scope.date);
        }
    }

    (function init() {
    	scope.date_format = window.constants.DATE_FORMAT ? window.constants.DATE_FORMAT : 'MM/dd/yyyy';
    	scope.DatePickerOpened = false;
        initDisplayDateFormat();
    })();

});

DatePicker.directive('argodatepicker', function($compile) {
	return {
		restrict: "E",
		replace: true,
		scope: {
			namespace: "@",
			date: "="
		},
		controller: 'DatePickCtrl',
		templateUrl: window.constants.ABS_URI + 'partials/datepicker.html'
	};
});


var Helper = angular.module('Helper', [])
.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});

Helper.factory('HelperFactory', function ($filter) {
    return {
        datetime_converter: function(datetime) {
            if(datetime) {
                var date_format = window.constants.DATETIME_FORMAT ? window.constants.DATETIME_FORMAT : 'HH:mm MM/dd/yyyy';
                datetime = datetime.replace(/-/g,"/");
                return $filter('date')(new Date(datetime), date_format);
            }

        },
        date_converter: function(datetime) {
            if(datetime) {
                var date_format = window.constants.DATE_FORMAT ? window.constants.DATE_FORMAT : 'MM/dd/yyyy';
                datetime = datetime.replace(/-/g,"/");
                return $filter('date')(new Date(datetime), date_format);
            }

        },
        iso_8601_converter: function(isoDateTimeString) {
          if(isoDateTimeString) {
              var parts = isoDateTimeString.match(/\d+/g);
              var isoTime = Date.UTC(parts[0], parts[1] - 1, parts[2], parts[3], parts[4], parts[5]);
              var date_format = "MMMM, \xa0 yyyy";
              return $filter('date')(new Date(isoTime), date_format);
          }
        }
    };
});

Helper.filter('capitalize', function() {
    return function(input) {
      return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});