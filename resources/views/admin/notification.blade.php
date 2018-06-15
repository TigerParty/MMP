@extends('master/main')

@section('js_block')
    <script src="{{ asset('legacy/js/notification.js') }}"></script>
@stop

@section('css_block')

@stop

@section('content')

<div class="container" id="manage-notification"  ng-app="NotificationApp" ng-controller="IndexCtrl as ctrl" >
    <div class="row header">
        <div class="col-md-12">
            <h1>Manage Email and SMS List</h1>
        </div>
    </div>
    <div class="row pagination-row">
        <div class="col-md-12">
            <ul class="pagination  pull-right">
                <li class="first hidden-xs">
                    <a href="#" ng-click="ctrl.goToPage(0)" ng-class="{'non-active': ctrl.currentPage == 0 }">
                        First
                    </a>
                </li>
                <li>
                    <a href="#" ng-click="ctrl.goToPage(ctrl.currentPage-1)" ng-class="{'non-active': ctrl.currentPage == 0 }">
                        <span class="glyphicon glyphicon-triangle-left"></span>
                    </a>
                </li>

                <li ng-repeat="page in ctrl.paginations">
                    <a href="#" class="ng-clock"
                        ng-class="{'active': ctrl.currentPage == page}"
                        ng-click="ctrl.goToPage(page)"
                        ng-bind="page+1"
                    ></a>
                </li>

                <li>
                    <a href="#" ng-click="ctrl.goToPage(ctrl.currentPage+1)" ng-class="{'non-active': ctrl.currentPage == ctrl.lastPage }">
                        <span class="glyphicon glyphicon-triangle-right"></span>
                    </a>
                </li>
                <li class="last hidden-xs">
                    <a href="#" ng-click="ctrl.goToPage(ctrl.lastPage)" ng-class="{'non-active': ctrl.currentPage == ctrl.lastPage }">
                        Last
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row filter">
        <div class="col-md-12">
            <div class="col-xs-12 col-sm-4 col-md-4 search-field">
                <div class="input-group">
                    <input type="text"
                        class="form-control"
                        placeholder="Search"
                        ng-model="ctrl.conditions.keyword"
                    >
                    <div class="input-group-addon" role="button" tabindex="0" ng-click="ctrl.queryProjects()">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-2 col-md-2 select-field"
                ng-repeat="(index, label) in ctrl.labels"
            >
                <select class="form-control"
                    ng-model="ctrl.conditions.regions[index]"
                    ng-options="region.id as region.name for region in ctrl.regions[index] track by region.id"
                    ng-change="ctrl.regionChanged(index)"
                    ng-disabled="!ctrl.regions[index].length"
                >
                    <option value="" ng-bind="label.name | regionTitleize "></option>
                </select>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-3 select-field">
                <select class="form-control" ng-model="ctrl.orderBy" ng-change="ctrl.queryProjects()">
                    <option value="">- Sort By -</option>
                    <option value="title">Alphabetical order</option>
                    <option value="updated_at">Most recently updated</option>
                    <option value="created_at">Time of Creation</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row main-table">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordred table-striped">
                    <thead>
                        <th>#</th>
                        <th>Record Name</th>
                        <th class="text-capitalize ng-cloak"
                            ng-repeat="label in ctrl.labels">
                            <% label.name %>
                        </th>
                        <th>Edit Email List</th>
                        <th>Edit SMS List</th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="(index, project) in ctrl.projects | limitTo:ctrl.itemPerPage:ctrl.currentPage*ctrl.itemPerPage">
                            <td ng-bind="index + 1"></td>
                            <td ng-bind="project.title"></td>
                            <td class="ng-cloak" ng-repeat="region in project.regions">
                                <% region.name %>
                            </td>
                            <td>
                                <a class="btn btn-info add-email" ng-click="ctrl.openEmailModal(project.id)">
                                    <span class="glyphicon glyphicon-envelope"></span> EMAIL
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-primary add-phone" ng-click="ctrl.openSMSModal(project.id)">
                                    <span class="glyphicon glyphicon-phone"></span> SMS
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div><!-- .container -->

@stop
