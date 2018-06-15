@extends('master/admin_main')

@section('css_block')
@stop

@section('js_block')
  <script src="{{ asset('legacy/js/attach-uploader.js') }}"></script>
  <script src="{{ asset('legacy/js/project_admin.js') }}"></script>
  <script>
    window.init_data = {
      parentId : {!! $parent_id ? (int)$parent_id : 'null' !!},
      containerId : {!! $container_id ? (int)$container_id : 'null' !!},
      createdBy: {!! (Auth::check()) ? "'".Auth::user()->name."'" : '' !!}
    };
  </script>
@stop

@section('content')
<div class="container-fluid border-bottom batch-entry" ng-app="ProjectAdminApp" ng-controller="BatchCtrl as ctrl">
  <div class="row">
    <div class="col-md-offset-1 col-md-10 border-left-right">
      <div class="col-md-12 padding-left-zero">
        <ol class="breadcrumb project-admin-breadcrumb text-uppercase">
          <li class="breadcrumb-item" ng-repeat="(index, breadcrumb) in ctrl.breadcrumbs">
            <a ng-href="<%breadcrumb.url%>" ng-bind="breadcrumb.title"></a>
          </li>
          <li class="breadcrumb-item">
            <a>Batch Entry</a>
          </li>
        </ol>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-offset-1 col-md-10 border-left-right">
      <p>1. Select Data-Entry form</p>

      <div class="form-group select-entry-form-block">
        <div class="row padding-left-zero">
          <div class="col-md-2">
            <select class="form-control border-radius-zero"
              ng-model="ctrl.selectedForm"
              ng-options="form.name for form in ctrl.forms track by form.id"
              ng-change="ctrl.selectedFormChanged()">
              <option value="">- Select Form -</option>
            </select>
          </div>
        </div>
      </div>

      <p>2. Use Microsoft Excel to enter Data</p>
      <div class="sub-items-block">
        <p>
          2.1 Download template
          <button ng-click="ctrl.onDownloadClick()"
             ng-disabled="ctrl.isExportingExcel"
             class="btn btn-default btn-sm  border-radius-zero template-btn">Download Template
             <img src="{{ asset('images/animation/loading.gif') }}"
                  ng-if="ctrl.isExportingExcel"
                  height="18px">
          </button>
        </p>
        <p>2.2 Enter data into template</p>
        <p>
          2.3 Upload template with data filled out
          <button class="btn btn-default btn-sm border-radius-zero template-btn"
            ng-click="ctrl.toggleUploadModal()"
          >Upload Template</button>
        </p>
        <p>- OR -</p>
        <p>Use Batch Entry table to enter data 20 rows at a time</p>
        <div ng-if="ctrl.submitSuccessCount > 0 || ctrl.submitFailCount > 0">
          Result:
          <p class="color-red" ng-if="ctrl.projects.errorColumns.connectionError">Server response error.</p>
          <p class="color-green"  ng-if="ctrl.submitSuccessCount > 0">Success: <% ctrl.submitSuccessCount %> entries created successfully.</p>
          <p class="color-red"  ng-if="ctrl.submitFailCount > 0">Error: Please correct the entries below.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-offset-1 col-md-10 border-left-right overflow-auto">
      <batch-inputs
        region-labels="ctrl.regionLabels"
        selected-form="ctrl.selectedForm"
        projects="ctrl.projects"
        created-by="ctrl.createdBy"
        container-name="ctrl.container.name"
        class="batch-inputs-block"
      ></batch-inputs>
    </div>
  </div>

  <div class="row">
    <div class="col-md-offset-1 col-md-10 border-left-right">
      <br>
      <button type="submit" class="btn project-admin-submit-btn"
        ng-click="ctrl.allSubmitSuccess = true; ctrl.singleRowSubmit(0);"
        ng-disabled="ctrl.requestBuffering">Submit</button>
      <button type="button" class="btn project-admin-cancel-btn"
        ng-click="ctrl.cancelForm()"
      >Cancel</button>
      <div class="row">
        <br>
      </div>
    </div>
  </div>

</div>
@stop
