@extends('master/main')

@section('css_block')
	<style type="text/css">
    .control-label:first-letter {
      text-transform: uppercase;
    }
  </style>
@stop

@section('js_block')
	<script>
		window.init_data = {
			project : {!! old('project') ? json_encode(old('project')) : $project !!},
			report : {!! old('report') ? json_encode(old('report')) : '{}' !!},
			regions : {!! $regions !!},
			dynamic_forms : {!! $dynamic_forms !!},
			pms_levels : {!! $pms_levels !!},
		}
	</script>
	<script src="{{ asset('js/attach-uploader.js') }}"></script>
	<script src="{{ asset('js/report.js') }}"></script>
@stop

@section('content')
<div class="container report-create">
	<div ng-app="ReportApp" ng-controller="CreateCtrl">
		<div class="row form-group"  >
			<h2 class="col-md-3">{{ Lang::get('form.report.title.create') }}</h2>
		</div>

		<div class="row form-group">
			<h4 class="control-label col-md-2">{{ Lang::get('form.report.component.info') }}</h4>
			<hr>
		</div>

		@if($errors->any())
      <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
          {{ $error }}<br>
        @endforeach
      </div>
    @endif

		<form action="{{ asset("project/$project->id/report") }}" method="POST">
		{{ csrf_field() }}
			<div class="row">
				<div class="col-md-6">
					<div class="row form-group">
						<label class="control-label col-md-5">{{ Lang::get('form.report.field.report_to') }}</label>
						<label class="control-label col-md-7">{{{ $project->title }}}</label>
					</div>
					<div class="row form-group" ng-repeat="regions in region_levels track by $index">
            <label class="control-label col-md-5"><% regions[0].label_name %></label>
            <div class="col-md-7">
              <select class="form-control" name="project[regions][<% $index%>][id]"
                      ng-model="project.regions[$index]"
                      ng-options="region.name for region in regions track by region.id"
                      ng-change="selectRegion($index, project.regions[$index].id)">
                <option value="">- {{ Lang::get('form.project.field.select') }} <% regions[0].label_name %> -</option>
              </select>
            </div>
          </div>
					<div class="row form-group">
						<label class="control-label col-md-5">{{ Lang::get('form.category.name') }}</label>
						<div class="col-md-7 category-list">
							<div class="categories">
								<cgygroup categories="categories" class=""></cgygroup>
							</div>
						</div>
					</div>

					<div class="row form-group">
						<label class="control-label col-md-5">{{ Lang::get('form.report.field.description') }}</label>
						<div class="col-md-7">
							<textarea class="form-control" rows="5" cols="50" name="report[description]"
								ng-model="report.description">
							</textarea>
						</div>
					</div>
					<div class="row form-group">
						<label class="control-label col-md-5 position-adjust">{{ Lang::get('form.report.field.lat') }}</label>
						<div class="col-md-7">
							<input type="text" class="form-control" name="report[lat]" placeholder="(String in 255 limit, required)"
								ng-model="report.lat">
						</div>
					</div>
					<div class="row form-group">
						<label class="control-label col-md-5 position-adjust">{{ Lang::get('form.report.field.lng') }}</label>
						<div class="col-md-7">
							<input type="text" class="form-control" name="report[lng]" placeholder="(String in 255 limit, required)"
								ng-model="report.lng">
						</div>
					</div>
					<div class="row form-group">
						<label class="control-label col-md-5 position-adjust">{{ Lang::get('form.report.field.created_by') }}</label>
						<div class="col-md-7">
							<input type="text" class="form-control" placeholder="(String in 255 limit, required)" readonly="true"
								value="{{ Auth::user()->name}}">
						</div>
					</div>

					<div class="row form-group">
						<label class="control-label col-md-5 position-adjust">{{ Lang::get('form.report.field.dynamic_form.name') }}</label>
						<div class="col-md-7">
							<input type="hidden" name="report[dynamic_form][is_photo_required]" ng-value="report.dynamic_form.is_photo_required">
							<select class="form-control" name="report[dynamic_form][id]"
							        ng-init="initDyncForm();"
				              ng-model="report.dynamic_form"
				              ng-options="dynamic_form.name for dynamic_form in dynamic_forms track by dynamic_form.id">
			                <option value="">{{ Lang::get('form.report.field.dynamic_form.select') }}</option>
			        </select>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<leaflet
						center="map.center"
						defaults="map.defaults"
						markers="map.markers"
						width="100%" height="560px">
					</leaflet>
				</div>
			</div>



			<div class="row form-group">
				<h4 class="control-label col-md-3">{{ Lang::get('form.report.component.dynamic_fields') }}</h4>
				<hr>
			</div>
			<div class="row form-group" ng-repeat="(index, field) in report.dynamic_form.fields">
				<df field="field" fields="report.dynamic_form.fields" index="index" use-default="report.dynamic_form.use_default"></df>
			</div>

			@if( argo_is_accessible(Config::get('argodf.admin_function_priority')) )
				<div class="row form-group">
					<h4 class="control-label col-md-3">{{ Lang::get('form.admin.name') }}</h4>
					<hr>
				</div>
				<div class="row form-group">
					<label class="control-label col-md-3 position-adjust">{{ Lang::get('form.admin.view_level.name') }}</label>
					<div class="col-md-4">
						<select class="form-control" name="report[view_level]"
			              ng-model="report.view_level"
			              ng-options="pms_level.name for pms_level in pms_levels track by pms_level.id"
			            >
		              	<option value="">{{ Lang::get('form.admin.view_level.select') }}</option>
		              	</select>
					</div>
				</div>

				<div class="row form-group">
					<label class="control-label col-md-3 position-adjust">{{ Lang::get('form.admin.edit_level.name') }}</label>
					<div class="col-md-4">
						<select class="form-control" name="report[edit_level]"
			              ng-model="report.edit_level"
			              ng-options="pms_level.name for pms_level in pms_levels track by pms_level.id"
			            >
		              	<option value="">{{ Lang::get('form.admin.edit_level.select') }}</option>
		              	</select>
					</div>
				</div>
			@endif

			<div class="row form-group">
				<h4 class="control-label col-md-3">{{ Lang::get('form.attachment.image') }}</h4>
				<hr>
			</div>

			<div class="col-md-12" >
				<div class="col-md-3 report-edit-thumbnail" ng-repeat="(index, image) in report.images">
					<input type="hidden" name="attachIDs[]" value="<% image.id %>">
					<div class="delete">
						<a href="" ng-click="deleteImage(index)">Delete</a>
					</div>
					<img class="col-md-12" ng-src="{{ asset('file/<% image.id %>') }}"  alt="">
				</div>
			</div>

			<div class="row form-group">
				<h4 class="control-label col-md-2">{{ Lang::get('form.attachment.file') }}</h4>
				<div class="col-md-10"><hr></div>
			</div>

			<div class="col-md-12">
				<div class="row form-group" ng-repeat="(index, attachment) in report.attachments">
					<input type="hidden" name="attachIDs[]" value="<% attachment.id %>">
					<div class="col-md-6">
						<a href="{{ asset('file/<% attachment.id %>') }}"><% attachment.name %></a>
					</div>
					<div class="col-md-2">
						<a href="" ng-click="deleteAttachment(index);">Delete</a>
					</div>
				</div>
			</div>

			<div class="row form-group">
				<h4 class="control-label col-md-3">{{ Lang::get('form.attachment.upload') }}</h4>
				<div class="col-md-9"><hr></div>
			</div>

			<div class="row form-group">
				<filegroup files=""></filegroup>
				<fileuploader></fileuploader>
			</div>

			<div class="row form-group">
				<div class="col-md-5">
	              	<button type="button" class="btn btn-default" onclick="location.href='/project/{{ $project->id }}'" >Cancel
	              	</button>
	              	<input type="submit" value="Submit" class="btn btn-default" ng-click="" ng-disabled="upload_in_progress">
	            </div>
	        </div>

		</form>
	</div>
</div><!-- .container -->
@stop