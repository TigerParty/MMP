@extends('master/main')
@section('css_block')
@stop

@section('js_block')
	@if(config('services.fb_comment_plugin.app_id'))
		<meta property="fb:app_id" content="{{ config('services.fb_comment_plugin.app_id') }}" />
		<script>
			(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.7&appId={{ config('services.fb_comment_plugin.app_id') }}";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		</script>
	@endif
<script src="{{ asset('js/attach-uploader.js') }}"></script>
<script src="{{ asset('js/report.js') }}"></script>
<div id="fb-root"></div>
<script>
	window.init_data = {
		report : {!! $report !!}
	};
</script>
@stop

@section('content')
<div class="container">
	<div ng-app="ReportApp" ng-controller="ShowCtrl">

		<div class="row">
			<div class="col-md-8">
				<h3 class="sub-header"><% report.basic_info.project_title %></h3>
			</div>

			<div class="col-md-4">
				<br>
				<div class="pull-right">
				@if( argo_is_accessible($project->edit_level->id) )
					<a class="btn btn-default" ng-click="autoMergeProjectValue()" ng-disabled="merged">{{ Lang::get('form.admin.auto_merge') }}</a>
				@endif
				</div>
			</div>
		</div>

		@if( argo_is_accessible(Config::get('argodf.admin_function_priority')) )
			<div class="panel panel-success">
				<div class="panel-heading"><h4>{{ Lang::get('form.admin.name') }}</h4></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<label>{{ Lang::get('form.admin.edit_level.name') }} :</label>
							<span cass="col-md-3"><% report.basic_info.edit_level %></span>
						</div>
						<div class="col-md-6">
							<label>{{ Lang::get('form.admin.view_level.name') }} :</label>
							<span><% report.basic_info.view_level %></span>
						</div>
					</div>
				</div>
			</div><!-- .panel -->
		@endif

		<div class="row form-group">
			<div class="col-md-6">
				<h4>{{ Lang::get('form.report.component.info') }}</h4><hr>

					<div class="form-group">
						<label for="" class="col-md-4">{{ Lang::get('form.category.name') }}:</label>
						<div>
							<p class="col-md-8">
								<span class="label label-info" ng-repeat="category in report.categories">
									<% category.name %>
								</span>
							</p>
						</div>
					</div>

					<div class="col-md-12">
						<div class="row form-group">
							<strong class="col-md-4">{{ Lang::get('form.report.field.report_to') }} :</strong>
							<div class="col-md-8">
								<span><% report.basic_info.project_title %></span>
							</div>
						</div>
						<div class="row form-group" ng-repeat="region in report.basic_info.regions">
		        	<strong class="col-md-4"><% region.label_name %>: </strong>
		        	<div class="col-md-8">
								<span><% region.name %></span>
							</div>
		        </div>
						<div class="row form-group">
							<strong class="col-md-4">{{ Lang::get('form.report.field.version') }} :</strong>
							<div class="col-md-8">
								<span><% report.basic_info.version %></span>
							</div>
						</div>
						<div class="row form-group">
							<strong class="col-md-4">{{ Lang::get('form.report.field.description') }} :</strong>
							<div class="col-md-8">
								<span><% report.basic_info.description %></span>
							</div>
						</div>
						<div class="row form-group">
							<strong class="col-md-4">{{ Lang::get('form.report.field.lat') }} :</strong>
							<div class="col-md-8">
								<span><% report.basic_info.lat %></span>
							</div>
						</div>
						<div class="row form-group">
							<strong class="col-md-4">{{ Lang::get('form.report.field.lng') }} :</strong>
							<div class="col-md-8">
								<span><% report.basic_info.lng %></span>
							</div>
						</div>
						<div class="row form-group">
							<strong class="col-md-4">{{ Lang::get('form.report.field.created_by') }} :</strong>
							<div class="col-md-8">
								<span><% report.basic_info.created_by %></span>
							</div>
						</div>
						<div class="row form-group">
							<strong class="col-md-4">{{ Lang::get('form.report.field.updated_by') }} :</strong>
							<div class="col-md-8">
								<span><% report.basic_info.updated_by %></span>
							</div>
						</div>
						<div class="row form-group">
							<strong class="col-md-4">{{ Lang::get('form.report.field.updated_at') }} :</strong>
							<div class="col-md-8">
								<span><% report.basic_info.updated_at %></span>
							</div>
						</div>
						<div class="row form-group">
							<strong class="col-md-4">{{ Lang::get('form.report.field.dynamic_form.name') }} :</strong>
							<div class="col-md-8">
								<span><% report.basic_info.form_name %></span>
							</div>
						</div>
						<div ng-if="report.notifications.length > 0">
							<div class="row form-group">
								<strong class="col-md-4">{{ Lang::get('form.report.field.assigned_to') }}:</strong>
								<div class="col-md-8">
									<span ng-repeat="notification in report.notifications">
										<% notification.receiver %><br>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="row form-group">
							<h4 class="control-label">{{ Lang::get('form.report.component.dynamic_fields') }}</h4>
							<hr>
						</div>
						<div ng-if="report.fields.length > 0">
							<div class="row form-group" ng-show="field.watch ? field.watch[0].value == field.show_if[field.watch[0].id][0] : true" ng-repeat="field in report.fields">
								<strong class="col-md-4"><% field.name %></strong>
								<div class="col-md-8" ng-if="field.field_template_id != 8">
									<span><% field.value %></span>
								</div>
								<div class="col-md-8" ng-if="field.field_template_id == 8">
									<span>
										Average speed: <% field.value.avg_speed %> km/hr<br>
										Distance: <% field.value.tracker_distance %> km<br>
				            Start time: <% field.value.start_at %><br>
				            End time: <% field.value.end_at %><br>
				          </span>
								</div>
							</div>
						</div>
					</div>


				</div><!-- .col-md-6 -->

			<div class="col-md-6">
				<h4 class="control-label">{{ Lang::get('form.report.component.location') }}</h4><hr>
				<leaflet
					center="map.center"
					defaults="map.defaults"
					markers="map.markers"
					width="100%" height="400px">
				</leaflet>
			</div>
		</div>


		<div class="row form-group">
			<h4 class="control-label col-md-2">{{ Lang::get('form.attachment.image') }}</h4>
			<div class="col-md-10"><hr></div>
		</div>
		<div class="col-md-12">
			<div class="report-profile col-md-3" ng-repeat="image in report.attachments">
				<img class="col-md-12" ng-if="image.type.includes('image')" ng-src="<% image.path %>" width="100%" alt="">
			</div>
		</div>
		<div class="row form-group">
			<h4 class="control-label col-md-2">{{ Lang::get('form.attachment.file') }}</h4>
			<div class="col-md-10"><hr></div>
		</div>

		<div class="col-md-12">
			<div class="row form-group">
				<div ng-repeat="attachment in report.attachments">
					<div ng-if="!attachment.type.includes('image')">
						@if(!config('services.virustotal.apikey'))
							<a href="/file/<% attachment.id %>"><% attachment.name %></a>
						@else
							<a ng-if="attachment.status == 'negatives'" href="/file/<% attachment.id %>"><% attachment.name %></a>
							<a ng-if="attachment.status != 'negatives'"><% attachment.name %></a>
						@endif
					</div>
				</div>
			</div>
		</div>

		@if(config('services.fb_comment_plugin.app_id'))
		<div class="row form-group">
			<h4 class="control-label col-md-2">{{ Lang::get('form.report.component.comments') }}</h4>
			<hr>
			<div class="fb-comments"
				data-href="{{ Request::url() }}"
				data-numposts="3"
				data-width="100%"
				data-order-by="reverse_time">
			</div>
		</div>
		@endif

		<div class="row form-group">
			<a href="/project/<% report.basic_info.project_id %>">
				<button class="btn btn-default">{{ Lang::get('form.report.btn.back') }}</button>
			</a>
		</div>
	</div>
</div><!-- .container -->
@stop