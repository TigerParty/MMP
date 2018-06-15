@extends('master/main')

@section('css_block')
@stop

@section('js_block')
  <script src="{{ asset('legacy/js/status.js') }}"></script>
  <script>
    window.init_data = {
      status_labels : {!! json_encode($status_labels, JSON_NUMERIC_CHECK) !!}
    };
  </script>
@stop

@section('content')
  <div class="container admin-status" ng-app="StatusApp" ng-controller="StatusIndexCtrl">
    <div class="col-md-offset-2 col-md-8">
      <div class="row">
        <div class="col-md-10 col-xs-9">
          <h3 class="sub-header">Manage Status Labels</h3>
        </div>
        <div class="col-md-2 padding-right-zero">
          <br>
          <a class="btn btn-info pull-right save-btn" href="" ng-click="save_changes()" ng-disabled="loading">Save Changes</a>
        </div>
      </div>

      <div class="row">
        <a class="btn btn-warning pull-right add-btn info-row" ng-click="show_label_field = true">＋ Add New </a>
      </div>

      <br>

      <div class="row" ng-show="show_label_field">
        <div class="col-md-8 col-sm-8 col-xs-7">
            <input type="text" class="form-control black-placeholder" ng-model="new_status_name" placeholder="Enter Status Label here">
        </div>
        <div class="col-md-offset-2 col-md-1 col-sm-offset-2 col-sm-1 col-xs-offset-1 col-xs-2">
          <a class="btn btn-default pull-right gray-btn margin-right-15" href="" ng-click="add_status()">Save</a>
        </div>
        <div class="col-md-1 col-sm-1 padding-right-zero col-xs-2">
          <a class="btn btn-default pull-right dark-gray-btn" ng-click="show_label_field = false">Cancel </a>
        </div>
      </div>

      <br>
      <br>

      <div ng-repeat="status_label in status_labels track by $index">
        <div class="row">
          <div class="col-md-8 col-sm-8 col-xs-12">
              <div class="form-control white-bg" ng-show="!status_label.edit"><% status_label.name %></div>
              <input type="text" class="form-control" style="background: white" ng-model="status_label.name" ng-show="status_label.edit" ng-focus="status_label.edit" placeholder="Enter Status Label here">
          </div>
          <div class="col-md-2 col-sm-2 col-xs-4">
            <div class="radio">
              <label><input type="radio" ng-model="default.value" ng-value="status_label.default" ng-click="default_change($index)">Default</label>
            </div>
          </div>
          <div class="col-md-1 col-sm-1 col-xs-4">
            <a class="btn btn-default pull-right gray-btn margin-right-15" href="" ng-click="status_label.edit = true" ng-show="!status_label.edit">Edit</a>
            <a class="btn btn-default pull-right gray-btn margin-right-15" href="" ng-click="update_status($index)" ng-show="status_label.edit">Save</a>
          </div>
          <div class="col-md-1 col-sm-1 col-xs-4 padding-right-zero">
            <a class="btn btn-default pull-right dark-gray-btn" href="" ng-click="delete_status($index)">delete</a>
          </div>
        </div>
        <br>
      </div>
    </div>
  </div>
@stop
