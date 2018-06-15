@extends('master/admin_main')

@section('css_block')
@stop

@section('js_block')
  <script src="{{ asset('legacy/js/attach-uploader.js') }}"></script>
  <script src="{{ asset('legacy/js/project_admin.js') }}"></script>
  <script>
    window.init_data = {
      projectId : parseInt({!! $projectId !!})
    };
    function openNewWindow(url) {
      window.open(url, "PrintWindow", "width=800, height=500");
    }
  </script>
@stop

@section('content')
<div class="container-fluid border-bottom admin-project-show" ng-app="ProjectAdminApp" ng-controller="ShowCtrl as ctrl">
  <div class="row">
    <div class="col-md-offset-1 col-md-10 border-left-right">
      <ol class="breadcrumb project-admin-breadcrumb">
        <li class="breadcrumb-item text-uppercase" ng-repeat="(index, breadcrumb) in ctrl.breadcrumbs">
          <a ng-href="<%breadcrumb.url%>" ng-bind="breadcrumb.title"></a>
        </li>
      </ol>
      <div class="pull-right function-btns-block">
        <span>Add new data: </span>
        <div class="btn-group" uib-dropdown>
          <button type="button"
                  class="btn btn-default"
                  uib-dropdown-toggle>
            <% ctrl.selectedForm.name %>
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu"
              uib-dropdown-menu
              role="menu">
            <li ng-repeat="form in ctrl.forms"
                role="menuitem">
              <a href
                 ng-click="ctrl.onFormSelected(form)"
                 ng-style="{'font-weight': form.fontStyle}">
                 <% form.name %>
              </a>
            </li>
          </ul>
        </div>
        <a ng-href="<% ctrl.absUri + 'admin/project/' + ctrl.projectId + '/form/' + ctrl.selectedForm.id + '/' + ctrl.selectedForm.goMode %>"
           class="color-black">
          <button class="project-admin-button"
                  ng-disabled="!ctrl.selectedForm.id">Go</button>
        </a>
      </div>
    </div>
  </div>
@if ($cardRule)
  <div class="row">
    <div class="col-md-offset-1 col-md-10 border-left-right">
      <div class="pull-right function-btns-block">
          <button class="pull-right project-admin-button color-black" onclick='openNewWindow("{{ url("/admin/card/project/$projectId") }}")'>
              ID Card
          </button>
      </div>
    </div>
  </div>
@endif

  <basic-info-show
    project-id="ctrl.projectId"
    basic-info="ctrl.projectData.basic_info"
  ></basic-info-show>

  <form-show
    project-id="ctrl.projectId"
    forms="ctrl.projectData.forms"
  ></form-show>

  <div ng-repeat="(index, container) in ctrl.subcontainers">
    <div class="row">
      <div class="col-md-offset-1 col-md-10 border-left-right section-header">
        <div class="col-md-6 section-title">
          <span ng-bind="(container.name)"></span>
        </div>
        <div class="col-md-6 section-links pull-right">
          <a class="link"
            ng-href="<% ctrl.absUri + 'admin/project/' + ctrl.projectId + '/container/' + container.id + '/subproject/create/batch' %>"
            ng-bind="(container.name) + ' Batch Entry'"
          ></a>
          <a class="link"
            ng-href="<% ctrl.absUri + 'admin/project/' + ctrl.projectId + '/container/' + container.id + '/subproject/create' %>"
            ng-bind="'Create '+(container.name)"
          ></a>
        </div>
      </div>
    </div>

    <pagination-bar
      items-count="container.projectsCount"
      items-per-page="ctrl.itemsPerPage"
      forms="ctrl.forms"
      current-page="container.currentPage"
      conditions="container.conditions"
      mode="container.mode || 'show'"
      on-condition-change="ctrl.onSubcontainerConditionChange(index)"
    >
    </pagination-bar>

    <admin-project-list
      mode="container.mode"
      container-name="<%container.name%>"
      offset="container.currentPage * ctrl.itemsPerPage"
      region-labels="container.filters.region_labels"
      form="container.conditions.form"
      form-fields="container.itemFormFields"
      projects="container.projects"
      on-refresh="ctrl.onSubcontainerRefresh(index)"
    ></admin-project-list>

  </div> {{-- EOF ng-repeat subcontainers --}}
</div>
@stop
