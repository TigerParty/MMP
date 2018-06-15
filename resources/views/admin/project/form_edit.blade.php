@extends('master/admin_main')

@section('css_block')
@stop

@section('js_block')
  <script src="{{ asset('legacy/js/attach-uploader.js') }}"></script>
  <script src="{{ asset('legacy/js/project_admin.js') }}"></script>
  <script>
    window.init_data = {
      projectId : parseInt({!! $projectId !!}),
      formId : parseInt({!! $formId !!})
    };
  </script>
@stop

@section('content')
<div class="container-fluid border-bottom" ng-app="ProjectAdminApp" ng-controller="FormEditCtrl as ctrl" id="project-admin-form-edit">
  <div class="row">
    <div class="col-md-offset-1 col-md-10 border-left-right">
      <ol class="breadcrumb project-admin-breadcrumb text-uppercase">
        <li class="breadcrumb-item" ng-repeat="(index, breadcrumb) in ctrl.breadcrumbs">
          <a ng-href="<%breadcrumb.url%>" ng-bind="breadcrumb.title"></a>
        </li>
        <li class="breadcrumb-item">
          <a ng-bind="ctrl.formData.form_name"></a>
        </li>
        <li class="breadcrumb-item">
          <a>EDIT</a>
        </li>
      </ol>
    </div>
  </div>

  <div class="row" id="basic-info-create">
    <div class="col-md-offset-1 col-md-10 border-left-right">
     <form>
      {{-- Field 1 --}}
      <div class="form-group row" ng-repeat="(index, field) in ctrl.formData.fields">
        <label class="col-sm-4 col-md-3"
          ng-bind="field.name"
        ></label>
        <dynamic-field
          class="col-sm-8 col-md-4"
          mode="'edit'"
          control-class="form-control"
          template-key="field.key"
          options="field.options"
          value="ctrl.formData.form_field_values[field.id]"
          formula="field.formula"
        ></dynamic-field>
      </div>

      {{-- image section --}}
      <div class="row">
        <div class="col-md-12 ">
          <label class="image-label">Images:</label>
        </div>

        <div class="project-admin-image-section">
          <label class="col-sm-4 col-md-3 col-form-label">Upload New Photo</label>
          <div class="col-sm-8 col-md-9">
            <attach-uploader
              attach-ids="ctrl.newPhotoIds"
              attaches="ctrl.newPhotos"
              on-success="ctrl.uploadCheck()"
            ></attach-uploader>
          </div>

          <form-images-edit
            media-groups="ctrl.formData.media_groups"
          >
          </form-images-edit>

        </div>
      </div>
      <hr class="project-admin-hr" />

      <div class="form-group row">
        <div class="col-sm-10">
          <button type="submit" class="btn project-admin-submit-btn" ng-click="ctrl.updateForm()">Submit</button>
          <button type="button" class="btn project-admin-cancel-btn" ng-click="ctrl.cancelForm()">Cancel</button>
        </div>
      </div>
     </form>
    </div>
  </div>
</div>
@stop
