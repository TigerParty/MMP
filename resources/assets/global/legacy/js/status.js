var StatusApp = angular.module('StatusApp', []);

StatusApp.config(function($interpolateProvider, $httpProvider, $logProvider) {
	$interpolateProvider.startSymbol('<%');
	$interpolateProvider.endSymbol('%>');
	$logProvider.debugEnabled(false);
	$httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

StatusApp.factory('StatusFactory', function($http) {
	return {
		saveChanges : function(changes) {
			return $http.post(window.constants.ABS_URI + 'admin/status', {
				_token : window.constants.CSRF_TOKEN,
				changes : changes
			});
		}
	};
});

StatusApp.controller('StatusIndexCtrl', function($scope, StatusFactory) {
	$scope.add_status = function() {
		if ($scope.new_status_name != "" && $scope.new_status_name != null)
		{
			$scope.status_labels.unshift({"name": $scope.new_status_name, "default": 0});
			$scope.new_status_name = null;
			$scope.show_label_field = false;
		}
	};

	$scope.delete_status = function(index) {
		if('id' in $scope.status_labels[index])
		{
			$scope.deleted_status_ids.push($scope.status_labels[index].id);
		}

		$scope.status_labels.splice(index, 1);
	};

	$scope.update_status = function(index) {
		$scope.status_labels[index].edit = undefined;

		if('id' in $scope.status_labels[index])
		{
			$scope.status_labels[index].updated = true;
		}
	};

	$scope.default_change = function(index) {
		angular.forEach($scope.status_labels, function(status) {
			status.default = 0;
		});
		$scope.status_labels[index].default = 1;
		if($scope.status_labels[index].id)
		{
			$scope.status_labels[index].updated = true;
		}
		$scope.default.value = 1;
	};

	$scope.save_changes = function() {
		if($scope.loading)
		{
			return false;
		}

		$scope.loading = true;
		var changes = {
			updated_status : [],
			created_status : [],
			deleted_status_ids : $scope.deleted_status_ids
		};
		angular.forEach($scope.status_labels, function(status, index) {
			if(status.id && (status.updated || status.default == 1))
			{
				changes.updated_status.push(status);
			}
			else if(!status.id)
			{
				changes.created_status.push(status);
			}
		});

		StatusFactory.saveChanges(changes).then(function(response) {
			window.location.href = window.constants.ABS_URI + 'admin/status';
		})
		.catch(function(response) {
			$scope.loading = false;
		});
	};

	(function init() {
		$scope.status_labels = window.init_data.status_labels || [];

		$scope.deleted_status_ids = [];

		$scope.default = {'value':1};
	})();
});