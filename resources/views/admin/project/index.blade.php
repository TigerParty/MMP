@extends('master/admin_main')

@section('css_block')
@stop

@section('js_block')
  <script>
    window.init_data = {
      "items_per_page": {{ $items_per_page }},
      "container_id": {{ $container_id }},
      "container_form_id": {{ $container_form_id }},
    };
  </script>
  <script src="{{ asset('legacy/js/attach-uploader.js') }}"></script>
  <script src="{{ asset('legacy/js/project_admin.js') }}"></script>
@stop

@section('content')
<div class="container-fluid border-bottom admin-project-index" ng-app="ProjectAdminApp" ng-controller="RootIndexCtrl as ctrl">
  <div id="page-header" class="row">
    <div id="page-nav" class="col-md-offset-1 col-md-10 border-left-right">
      <div id="page-title" class="col-xs-12 col-sm-6 col-md-6">
        <h1>{{ $container_name }}</h1>
      </div>
      <div id="page-links" class="col-xs-12 col-sm-6 col-md-6 pull-right text-right">
        <span>
          <% ctrl.itemsCount %> {{ $container_name }}
        </span>
        <a class="btn btn-default" role="button" type="button" href="{{ asset("/admin/project/create/") }}">
          {{ trans('admin.create_new_link')." $container_name" }}
        </a>
        <a class="btn btn-default" role="button" type="button" href="{{ asset("/admin/project/create/batch") }}">
          {{ trans('admin.batch_entry_link') }}
        </a>
      </div>
    </div>
  </div>

  <filter-bar
    container-id="ctrl.containerId"
    filters="ctrl.filters"
    conditions="ctrl.conditions"
    on-condition-change="ctrl.onConditionChange()"
    on-refresh="ctrl.onRefresh()"
  >
  </filter-bar>

  <pagination-bar
    items-count="ctrl.itemsCount"
    items-per-page="ctrl.itemsPerPage"
    current-page="ctrl.currentPage"
    conditions="ctrl.conditions"
    mode="ctrl.mode"
    forms="ctrl.forms"
    on-condition-change="ctrl.onConditionChange()"
  >
  </pagination-bar>

  <admin-project-list
    mode="ctrl.mode"
    container-name="{{ $container_name }}"
    offset="ctrl.currentPage * ctrl.itemsPerPage"
    region-labels="ctrl.filters.region_labels"
    form="ctrl.conditions.form"
    form-fields="ctrl.itemFormFields"
    projects="ctrl.projects"
    on-refresh="ctrl.onRefresh()"
    show-scroll-tool='true'
  ></admin-project-list>

</div>
@stop
