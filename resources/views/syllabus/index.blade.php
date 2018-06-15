@extends('master/main')

@section('css_block')
@stop

@section('js_block')
    <script src="{{ asset('js/syllabus.js') }}"></script>
@stop

@section('content')
    <div class="syllabus" ng-app="SyllabusApp" ng-controller="SyllabusIndexCtrl as indexCtrl">
        <div class="container">
            <div class="row">
                <div class="col-xs-offset-6 col-xs-6 col-sm-offset-4 col-sm-8  col-md-offset-3 col-md-9 col-lg-offset-3 col-lg-9">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a>Syllabus</a></li>
                      <li class="breadcrumb-item" ng-if="indexCtrl.target.classes > -1"><a><% indexCtrl.syllabus.courses[indexCtrl.target.courses].title %></a></li>
                      <li class="breadcrumb-item"><a><% indexCtrl.target.title%></a></li>
                    </ol>
                    <div class="tips pull-right" >
                        <span ng-if="indexCtrl.target.courses == -1">Click the title of any course to download its' syllabus</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 padding-right-zero">

                    <div class="title"
                         ng-class="{'active': indexCtrl.target.courses == -1}"
                         ng-click="indexCtrl.onClickAllCourses(indexCtrl.syllabus.title)">
                         <% indexCtrl.syllabus.title%>
                    </div>

                    <ul class="sidebar-nav" ng-if="indexCtrl.syllabus.hasOwnProperty('courses')">
                      <li ng-class="{'open': indexCtrl.target.courses == courseIndex, 'active': (indexCtrl.target.courses == courseIndex && indexCtrl.target.classes == -1) }"
                          ng-repeat="(courseIndex,course) in indexCtrl.syllabus.courses">
                        <a ng-click="indexCtrl.onClickCourse(courseIndex, course)">
                            <span class="arrow"></span> <% course.title %>
                        </a>

                        <ul class="sub-sidebar-nav" ng-if="course.hasOwnProperty('classes')">
                            <li ng-class="{'active': indexCtrl.target.classes == classIndex}" ng-repeat="(classIndex, class) in course.classes">
                             <a ng-click="indexCtrl.onClickClass(courseIndex, classIndex, class)"> <% class.title %> </a>
                            </li>
                        </ul>

                      </li>
                    </ul>

                </div>
                <div class="col-xs-6 col-sm-8 col-md-9 col-lg-9">
                    <course-list ng-if="indexCtrl.mainContent.hasOwnProperty('courses')"
                                 main-content="indexCtrl.mainContent"
                                 courses="indexCtrl.mainContent.courses"
                                 target="indexCtrl.target"
                                 onclick-course="indexCtrl.onClickCourse(courseIndex, course)"
                                 onclick-class="indexCtrl.onClickClass(courseIndex, classIndex, class)"></course-list>
                    <course-item ng-if="!indexCtrl.mainContent.hasOwnProperty('courses')"
                                 main-content="indexCtrl.mainContent"
                                 classes="indexCtrl.mainContent" ></course-item>
                </div>
            </div>
        </div>
    </div>

@stop