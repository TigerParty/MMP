@extends('master/main')

@section('css_block')
@stop

@section('js_block')
  <script>
    window.init_data = {
      'dynamic_form' : {!! old() ? json_encode(old()) : '{}' !!},
      'field_templates' : {!! $field_templates !!},
      'pms_levels' : {!! $pms_levels !!}
    }
  </script>
  <script src="{{ asset('legacy/js/dync_form.js') }}"></script>
@stop

@section('content')
<div class="container form-create-edit">
  <div ng-app="DyncFormApp" ng-controller="CreateCtrl">

    <div class="row">
      <h3 class="col-md-12 sub-header">{{ Lang::get('form.dynamic_form.title.create') }}</h3>
    </div>

    @if($errors->any())
      <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
          {{ $error }}<br>
        @endforeach
      </div>
    @endif
    <form action="{{ asset('/admin/dync_form') }}" method="POST">
    {{ csrf_field() }}
      <div class="row form-group">
        <h4 class="control-label col-md-2">{{ Lang::get('form.dynamic_form.component.info') }}</h4>
        <hr>
      </div>
      <div class="row form-group">
        <label class="control-label col-md-2 position-adjust">{{ Lang::get('form.dynamic_form.field.name') }}</label>
        <div class="col-md-4">
          <input type="text" class="form-control white-bg white-bg" name="dynamic_form[name]" ng-model="dynamic_form.name">
        </div>

        <label class="col-md-2 position-adjust">
          Attachment is Mandatory
        </label>

        <div class="col-md-4">
          <div class="checkbox">
              <label>
                  <input type="checkbox" name="dynamic_form[is_photo_required]" ng-model="dynamic_form.is_photo_required" ng-value="1">
              </label>
          </div>
        </div>

      </div>
      <div class="row form-group">
        <h4 class="control-label col-md-2">{{ Lang::get('form.dynamic_form.component.fields') }}</h4>
        <hr>
      </div>
      <div ng-repeat="(f_idx, field) in dynamic_form.fields">
        <input type="hidden"
          name="dynamic_form[fields][<% f_idx %>][id]"
          value="<% field.id %>"
        >
        <div class="row form-group">
          <div class="col-md-6">
              <button type="button" class="btn btn-default disabled" ng-if="f_idx == 0" ng-disabled="true">
                  <i class="glyphicon glyphicon-arrow-up"></i>
              </button>
              <button type="button" class="btn btn-default" ng-if="f_idx > 0" ng-click="switchUp(f_idx)">
                  <i class="glyphicon glyphicon-arrow-up"></i>
              </button>
              <button type="button" class="btn btn-default" ng-if="f_idx < dynamic_form.fields.length - 1" ng-click="switchDown(f_idx)">
                  <i class="glyphicon glyphicon-arrow-down"></i>
              </button>
              <button type="button" class="btn btn-default disabled" ng-if="f_idx == dynamic_form.fields.length - 1" ng-disabled="true">
                  <i class="glyphicon glyphicon-arrow-down"></i>
              </button>
          </div>
          <div class="col-md-6">
            <button type="button" class="btn btn-default pull-right" ng-click="deleteDyncField(f_idx)" >{{ Lang::get('form.dynamic_form.btn.delete') }}</button>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-2 position-adjust">{{ Lang::get('form.dynamic_form.dync_field.name') }}</label>
          <div class="col-md-4">
            <input type="text" class="form-control white-bg white-bg" name="dynamic_form[fields][<% f_idx %>][name]"
                    ng-model="field.name">
          </div>
          <label class="col-md-2 position-adjust">{{ Lang::get('form.dynamic_form.dync_field.template_type.name') }}</label>
          <div class="col-md-4">
            <select class="form-control white-bg" name="dynamic_form[fields][<% f_idx %>][template]"
              ng-model="field.template"
              ng-options="field_template.name for field_template in field_templates track by field_template.id"
              ng-change="showOptionsByTemplate(field)"
              >
              <option value="">{{ Lang::get('form.dynamic_form.dync_field.template_type.select') }}</option>
            </select>
          </div>
        </div>
        <div class="row form-group">
          <label class="col-md-2 position-adjust">{{ Lang::get('form.dynamic_form.dync_field.default') }}</label>
          <div class="col-md-4">
            <input type="text" class="form-control white-bg" name="dynamic_form[fields][<% f_idx %>][default_value]"
                    ng-model="field.default_value">
          </div>

          <label class="col-md-2 position-adjust">
            Field is Mandatory
          </label>

          <div class="col-md-4">
            <div class="checkbox">
              <label>
                <input type="checkbox"
                    name="dynamic_form[fields][<% f_idx %>][is_required]"
                    ng-model="field.is_required"
                    ng-value="1"
                >
              </label>
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-2 position-adjust">{{ Lang::get('form.admin.edit_level.name') }}</label>
          <div class="col-md-4">
            <select class="form-control white-bg" name="dynamic_form[fields][<% f_idx %>][edit_level]"
              ng-model="field.edit_level"
              ng-options="pms_level.name for pms_level in pms_levels track by pms_level.id"
              >
              <option value="">{{ Lang::get('form.admin.edit_level.select') }}</option>
            </select>
          </div>
          <label class="col-md-2 position-adjust">{{ Lang::get('form.admin.view_level.name') }}</label>
          <div class="col-md-4">
            <select class="form-control white-bg" name="dynamic_form[fields][<% f_idx %>][view_level]"
              ng-model="field.view_level"
              ng-options="pms_level.name for pms_level in pms_levels track by pms_level.id"
              >
              <option value="">{{ Lang::get('form.admin.view_level.select') }}</option>
            </select>
           </div>
        </div>
        <div class="row form-group" ng-show="field.isShowOptions">
          <label class="col-md-2 position-adjust">{{ Lang::get('form.dynamic_form.dync_field.options') }}</label>
          <div class="col-md-10">
            <textarea class="form-control white-bg" cols="30" rows="4"
              name="dynamic_form[fields][<% f_idx %>][options]"
              ng-disabled="!field.isShowOptions"
              placeholder="Ex: option1,option2,option3, ...... etc"
              ng-model="field.options_to_show"
              ng-keyup="generateOptions(field)"
            >
            </textarea>
          </div>
        </div>
        <div class="form-group" ng-show="f_idx != 0">
          <div class="checkbox">
            <label>
              <input type="checkbox" ng-value="1" ng-change="showIfChange(field)" ng-model="field.showing_if_panel">{{ Lang::get('form.dynamic_form.dync_field.show_if.checkbox') }}
            </label>
          </div>
        </div>
        <div class="well well-sm" ng-show="field.showing_if_panel == 1">
          <div class="form-inline">
            <label for="" class="position-adjust">&nbsp;{{ Lang::get('form.dynamic_form.dync_field.show_if.if_the_answer_to') }}&nbsp;</label>
            <select class="form-control white-bg" name="dynamic_form[fields][<% f_idx %>][show_if][field_id]" ng-change="getSourceFieldOptions(field.show_if)" ng-model="field.show_if.field_id">
              <option value="">{{ Lang::get('form.dynamic_form.dync_field.show_if.select_question') }}</option>
              <option ng-repeat="fieldObj in dynamic_form.fields | filter:{'template':{'id':5}, 'id':'!'+field.id}" value="<% fieldObj.id %>" ng-selected="fieldObj.id == field.show_if.field_id"><% fieldObj.name %></option>
            </select>
            <label class="position-adjust">&nbsp;{{ Lang::get('form.dynamic_form.dync_field.show_if.is') }}&nbsp;</label>
            <select class="form-control white-bg" name="dynamic_form[fields][<% f_idx %>][show_if][equals]" ng-model="field.show_if.equals" ng-options="option for option in field.show_if.options track by option">
              <option value="">{{ Lang::get('form.dynamic_form.dync_field.show_if.select_answer') }}</option>
            </select>
          </div>
        </div>
        <hr>
      </div>

      <div class="row form-group">
        <div class="col-md-12">
          <button type="button" class="btn btn-default pull-right" ng-click="addNewDyncField()" >{{ Lang::get('form.dynamic_form.btn.add_field') }}</button>
        </div>
      </div>
      <div class="row form-group">
          <hr>
      </div>
      <div class="row form-group">
        <div class="col-md-5">
          <a class="btn btn-default" href="{{ asset('/admin/dync_form') }}" >{{ Lang::get('form.btn.cancel') }}
          </a>
          <input type="submit" value="{{ Lang::get('form.btn.submit') }}" class="btn btn-default" ng-click="unDisabled();"></input>
         </div>
      </div>
    </form>
  </div>
</div><!-- .container -->
@stop
