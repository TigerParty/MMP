@extends('master/main')

@section('css_block')
@stop

@section('js_block')
@stop

@section('content')
<div class="container">

	<div class="row">
		<div class="col-md-10">
			<h3 class="sub-header">{{ Lang::get('form.dynamic_form.title.show') }}</h3>
		</div>
		<div class="col-md-2">
			<br>
			<a class="btn btn-default pull-right" href="{{ asset('/admin/dync_form/'.$dynamic_form->id.'/edit') }}">
				{{ Lang::get('form.dynamic_form.btn.edit') }}
			</a>
		</div>
	</div>


	<div class="row form-group">
		<h4 class="control-label col-md-2">{{ Lang::get('form.dynamic_form.component.info') }}</h4>
		<div class="col-md-10"><hr></div>
	</div>
	<div class="col-md-12">
		<div class="row form-group">
			<strong class="col-md-2">{{ Lang::get('form.dynamic_form.field.name') }} :</strong>
			<div class="col-md-4">
				<span>{{{ $dynamic_form->name }}}</span>
			</div>
			<div class="col-md-6">
				@if($dynamic_form->is_photo_required == 1)
				<span>Attachment is Mandatory</span>
				@endif
			</div>
		</div>
	</div>

	<div class="row form-group">
		<h4 class="control-label col-md-2">{{ Lang::get('form.dynamic_form.component.fields') }}</h4>
		<div class="col-md-10"><hr></div>
	</div>
	<div class="panel group">
		@foreach($dynamic_form->fields as $field)
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="form-inline">
						<strong class="form-group">{{ Lang::get('form.dynamic_form.dync_field.name') }} :</strong>
						<div class="form-group">
							<span>{{{ $field->name }}}</span>
						</div>
						@if($field->is_required == 1)
						<div class="form-group">
							<span>(Field is Mandatory)</span>
						</div>
						@endif
					</div>

				</div>
				<div class="panel-body">
					<strong class="col-md-2">{{ Lang::get('form.dynamic_form.dync_field.template_type.name') }} :</strong>
					<div class="col-md-4">
						<span>{{{ $field->template->name }}}</span>
					</div>
					<strong class="col-md-2">{{ Lang::get('form.dynamic_form.dync_field.default') }} :</strong>
					<div class="col-md-4">
						@if( $field->default && $field->default != "")
							<span>{{{ $field->default }}}</span>
						@else
							<span>no value</span>
						@endif
					</div>
					<strong class="col-md-2">{{ Lang::get('form.admin.edit_level.name') }} :</strong>
					<div class="col-md-4">
						<span>{{{ $field->edit_level->name }}}</span>
					</div>
					<strong class="col-md-2">{{ Lang::get('form.admin.view_level.name') }} :</strong>
					<div class="col-md-4">
						<span>{{{ $field->view_level->name }}}</span>
					</div>
					@if( $field->options )
						<strong class="col-md-2">{{ Lang::get('form.dynamic_form.dync_field.options') }}:</strong>
						<div class="col-md-10 text-nowrap" style="overflow:hidden; text-overflow: ellipsis;">
							<span>{{{ implode(', ', $field->options) }}}</span>
						</div>
					@endif
					@if( $field->show_if_field_name != "")
						<strong class="col-md-12">This field is shown when &quot;{{ $field->show_if_field_value }}&quot; is selected in question &quot;{{ $field->show_if_field_name }}&quot;</strong>
					@endif
				</div>
			</div>
		@endforeach
	</div>
	<div class="row">
		<div class="col-md-12">
  		<a class="btn btn-default pull-right" href="{{ asset('/admin/dync_form') }}" >{{ Lang::get('form.dynamic_form.btn.back') }}</a>
	  </div>
	</div>
</div><!-- .container -->

@stop
