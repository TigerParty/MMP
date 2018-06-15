@extends('master/main')

@section('css_block')
<style type="text/css">
.project-title {
    color:black;
}
.project-title:hover{
    color:black;
    text-decoration: none;
}
</style>
@stop

@section('js_block')
@stop

@section('content')
<div id="overall-header" class="container"></div>

<div class="container">
    <div class="row">
        <div class="col-md-10">
            <a class="project-title" href="{{asset("project/$project->id")}}">
                <h3 class="sub-header">{{$project->title}} {{trans('report.index.title')}}</h3>
            </a>
        </div>
    </div>

    <div class="row form-group"><!-- form-group -->
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{trans('report.index.list_title.created_at')}}</th>
                            <th>{{trans('report.index.list_title.form_name')}}</th>
                            <th>{{trans('report.index.list_title.link')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($reports as $report)
                        <tr>
                          <td>{{$report->created_at}}</td>
                          <td>{{object_get($report, "dynamic_form.name", "")}}</td>
                          <td><a href="{{asset("/report/$report->id")}}">{{trans('report.index.list_title.link')}}</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td>{{trans('report.index.message.no_report')}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div><!-- .form-group -->
</div>

@stop
