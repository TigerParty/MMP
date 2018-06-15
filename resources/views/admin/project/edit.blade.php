@extends('master/admin_main')

@section('css_block')
@stop

@section('js_block')
  <script>
    window.init_data = {
      'project_id': parseInt({{ $project_id }}),
    }
  </script>
  <script src="{{ asset('legacy/js/project_admin.js') }}"></script>
@stop

@section('content')
<div class="container-fluid border-bottom admin-project-create-edit" ng-app="ProjectAdminApp" ng-controller="EditCtrl as ctrl" >
  <div class="row">
    <div class="col-md-offset-1 col-md-10 border-left-right">
    <ol class="breadcrumb project-admin-breadcrumb">
      <li class="breadcrumb-item text-uppercase" ng-repeat="(index, breadcrumb) in ctrl.breadcrumbs">
        <a ng-href="<%breadcrumb.url%>" ng-bind="breadcrumb.title"></a>
      </li>
      <li class="breadcrumb-item">
        <a>EDIT</a>
      </li>
    </ol>
    </div>
  </div>

  <div class="row" id="basic-info-create">

    <div class="col-md-offset-1 col-md-10 border-left-right">
      <h5 class="project-admin-section-title">Basic Info</h5>

      {{-- Name --}}
      <div class="form-group row">
        <label for="inputName" class="col-sm-4 col-md-3 col-form-label">
          Name
        </label>
        <div class="col-sm-8 col-md-4">
          <input type="text" class="form-control" id="inputName"
            ng-model="ctrl.project.title"
          >
        </div>
      </div>

      {{-- Regions --}}
      <div ng-repeat="(index, regionLabel) in ctrl.regionLabels">
          <div class="form-group row">
            <label for="selectRegion" class="col-sm-4 col-md-3 col-form-label text-capitalize">
              <% regionLabel %>
            </label>
            <div class="col-sm-8 col-md-4">
              <select class="form-control"
                ng-model="ctrl.project.regions[index]"
                ng-options="region.id as region.name for region in ctrl.regions[index]"
                ng-change="ctrl.updateRegions(index)"
                ng-disabled="!ctrl.regions[index].length"
              >
                <option value="">- Select <% regionLabel | titleize %> -</option>
              </select>
            </div>
          </div>
      </div>

      {{-- Date Record Created --}}
      <div class="form-group row">
        <label for="selectRegion" class="col-sm-4 col-md-3 col-form-label">
          Date Record Created
        </label>
        <div class="col-sm-8 col-md-4">
          <dynamic-field
            control-class="form-control"
            template-key="'date'"
            mode="'show'"
            value="ctrl.project.created_at"
            on-change="ctrl.onFieldChange('created_at')"
          ></dynamic-field>
        </div>
      </div>

      {{-- Date Record Updated --}}
      <div class="form-group row">
        <label for="selectRegion" class="col-sm-4 col-md-3 col-form-label">
          Date Record Updated
        </label>
        <div class="col-sm-8 col-md-4">
          <dynamic-field
            control-class="form-control"
            template-key="'date'"
            mode="'edit'"
            value="ctrl.project.updated_at"
            on-change="ctrl.onFieldChange('updated_at')"
          ></dynamic-field>
        </div>
      </div>

      {{-- Create by --}}
      <div class="form-group row">
        <label for="inputCreatedBy" class="col-sm-4 col-md-3 col-form-label">
          Created by
        </label>
        <div class="col-sm-8 col-md-4">
          <input type="text" class="form-control" placeholder="(String in 255 limit, required)"
            value="{!! (Auth::check()) ? Auth::user()->name : '' !!}"
            disabled
          >
        </div>
      </div>

      {{-- GPS location --}}
      <div class="form-group row">
        <label for="inputCreatedBy" class="col-sm-4 col-md-3 col-form-label">
          GPS Location
        </label>
        <div class="col-sm-8 col-md-4">
          <div class="pull-left">
            <input type="number" class="form-control"
            placeholder="latitude"
            ng-model="ctrl.project.lat"
            ng-model-options="{ debounce: 1000 }"
            ng-change="ctrl.updateMapLocation()"
          >
          </div>
          <div class="pull-right">
            <input type="number" class="form-control"
            placeholder="longitude"
            ng-model="ctrl.project.lng"
            ng-model-options="{ debounce: 1000 }"
            ng-change="ctrl.updateMapLocation()"
          >
          </div>
        </div>
      </div>

      {{-- Map --}}
      <div class="form-group row">
        <div class="co-sm-offset-4 col-md-offset-3 col-sm-8 col-md-4">
          <leaflet
            center="ctrl.map.center"
            defaults="ctrl.map.defaults"
            markers="ctrl.map.markers"
            width="100%" height="280px">
          </leaflet>
        </div>
      </div>

      {{-- Status --}}
      <div class="form-group row"
           ng-if="ctrl.statusVisible">
        <label for="inputCreatedBy" class="col-sm-4 col-md-3 col-form-label">
          Status
        </label>
        <div class="col-sm-8 col-md-4">
          <select class="form-control"
            ng-model="ctrl.project.status_id"
            ng-options="status.id as status.name for status in ctrl.statuses"
          >
            <option value="">- Select status -</option>
          </select>
        </div>
      </div>

      {{-- Upload Logo --}}
      <div class="form-group row">
        <label class="col-sm-4 col-md-3 col-form-label">
          {{ trans('admin.project.edit.upload_default_img') }}
        </label>
        <div class="col-sm-8 col-md-9">
          <attach-uploader
            attach-ids="ctrl.defaultImgIds"
            on-success="ctrl.onDefaultImgUploadFinished()"
          ></attach-uploader>
        </div>
        <div ng-if="ctrl.project.default_img_id"
          class="col-sm-offset-4 col-sm-3 col-md-offset-3 col-md-3"
        >
          <img class="img-thumbnail width-100-percent"
            ng-src="<% ctrl.absUri + 'file/' + ctrl.project.default_img_id %>"
          >
        </div>
      </div>

      {{-- Upload Cover Image --}}
      <div class="form-group row">
        <label class="col-sm-4 col-md-3 col-form-label">
          {{ trans('admin.project.edit.upload_cover_image') }}
        </label>
        <div class="col-sm-8 col-md-9">
          <attach-uploader
            attach-ids="ctrl.coverImgIds"
            on-success="ctrl.onCoverImgUploadFinished()"
          ></attach-uploader>
        </div>
        <div ng-if="ctrl.project.cover_image_id"
          class="col-sm-offset-4 col-sm-3 col-md-offset-3 col-md-3"
        >
          <span class="glyphicon glyphicon-remove pull-right img-remove-icon" aria-hidden="true" ng-click="ctrl.onRemoveCoverImageBtnClick()"></span>
          <img class="img-thumbnail width-100-percent"
            ng-src="<% ctrl.absUri + 'file/' + ctrl.project.cover_image_id %>"
          >
        </div>
      </div>

      {{-- Upload Attachment --}}
      <div class="form-group row">
        <label class="col-sm-4 col-md-3 col-form-label">
          {{ trans('admin.project.edit.upload_attaches') }}
        </label>
        <div class="col-sm-8 col-md-9">
          <multi-attach-uploader
            attach-ids="ctrl.project.attach_ids"
            attaches="ctrl.project.attaches"
          ></multi-attach-uploader>
        </div>
      </div>

      <hr class="project-admin-hr" />

      {{-- Edit/View Levels --}}
      <div class="form-group row level-row">
        <div class="col-md-1">
          <label>Edit level</label>
        </div>
        <div class="col-md-3">
          <select class="form-control"
            ng-model="ctrl.project.edit_level_id"
            ng-options="level.id as level.name for level in ctrl.levels"
          >
          </select>
        </div>

        <div class="col-md-1">
          <label>View level</label>
        </div>
        <div class="col-md-3">
          <select class="form-control"
            ng-model="ctrl.project.view_level_id"
            ng-options="level.id as level.name for level in ctrl.levels"
          >
          </select>
        </div>
      </div>

      <hr class="project-admin-hr" />

      <div class="form-group row">
        <div class="col-sm-4 col-md-3">
          <button class="btn project-admin-submit-btn"
            ng-click="ctrl.submitForm()"
          >
            Submit
          </button>
          <button type="button" class="btn project-admin-cancel-btn"
            ng-click="ctrl.cancelForm()">
            Cancel
          </button>
        </div>

        <div class="col-sm-8 col-md-9">
          <p class="text-danger"
            ng-repeat="(index, error) in ctrl.errors"
            ng-bind="error[0]"
          ></p>
        </div>
      </div>

    </div>
  </div> {{-- EOF: #basic-info-create --}}
</div> {{-- EOF: #project-admin-create --}}
@stop
