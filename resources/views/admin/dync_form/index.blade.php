@extends('master/main')

@section('css_block')
@stop

@section('js_block')
	<script>
		window.init_data = {
			'dynamic_forms' : {!! $dynamic_forms !!}
		}
	</script>
	<script src="{{ asset('legacy/js/dync_form.js') }}"></script>
@stop

@section('content')
<div class="container">
	<div ng-app="DyncFormApp" ng-controller="IndexCtrl">

		<div class="row">
			<div class="col-md-10">
				<h3 class="sub-header">{{ Lang::get('form.dynamic_form.name') }}</h3>
			</div>
			<div class="col-md-2">
				<br>
				<a class="btn btn-default pull-right" href="{{ asset('/admin/dync_form/create') }}">
					{{ Lang::get('form.dynamic_form.btn.create') }}
				</a>
			</div>
		</div>

		@if(Session::get('delete_info'))
	      <div class="alert alert-danger">
	        <span>Form "{{ Session::get('delete_info')->name }}" has been deleted!</span>
	      </div>
	    @endif


		<div class="row form-group">
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>{{ Lang::get('form.dynamic_form.field.name') }}</th>
								<th>{{ Lang::get('form.btn.link') }}</th>
								<th>{{ Lang::get('form.btn.delete') }}</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="(idx, dynamic_form) in dynamic_forms">
								<td><% dynamic_form.name %></td>
								<td><a href="/admin/dync_form/<% dynamic_form.id %>">view</a></td>
								<td><a href="#" ng-click="openConfirm(dynamic_form.id)">delete</a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-12">
        <a class="btn btn-default pull-right" href="{{ asset('/admin') }}" >{{ Lang::get('form.admin.btn.back') }}
        </a>
      </div>
  	</div>

	</div>
</div><!-- .container -->
@stop
