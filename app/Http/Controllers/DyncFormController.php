<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Exception;
use Log;

use App\Services\PermissionService;
use App\BusinessLogics\DyncFormBusinessLogic;
use App\Argo\DynamicForm;
use App\Argo\PermissionLevel;
use App\Argo\FieldTemplate;
use App\Argo\FormField;

class DyncFormController extends Controller
{

    public function index()
    {
        $dynamic_forms = DynamicForm::all();
        $array_data = array(
            'dynamic_forms' => $dynamic_forms
        );
        return view('admin.dync_form.index', $array_data);
    }

    public function indexApi()
    {
        $forms = DynamicForm::get([
            'id',
            'name'
        ]);
        return response()->json($forms, 200, [], JSON_NUMERIC_CHECK);
    }

    public function create()
    {
        $pms_levels = PermissionLevel::GetAvailableLevels(argo_current_permission());

        $array_data = array(
            'field_templates' => FieldTemplate::all(),
            'pms_levels'  => $pms_levels
        );

        return view('admin.dync_form.create', $array_data);
    }

    public function store(Request $request)
    {
        $df_bsns_logic = new DyncFormBusinessLogic();
        $df_bsns_logic->addFieldRule($request->input('dynamic_form'));

        if (!$df_bsns_logic->isValid($request->input('dynamic_form'), 'store')) {
            return redirect(asset('admin/dync_form/create'))
                ->withErrors($df_bsns_logic->getErrors())
                ->withInput($df_bsns_logic->getOriginInput());
        }

        $dynamic_form = $df_bsns_logic->getInput();

        try {
            DB::beginTransaction();

            $new_df = new DynamicForm;
            $new_df->name = $dynamic_form['name'];
            $new_df->is_photo_required = array_get($dynamic_form, 'is_photo_required', false);
            $new_df->save();

            if (array_key_exists('fields', $dynamic_form)) {
                foreach ($dynamic_form['fields'] as $key => $field) {
                    $new_field = new FormField;
                    $new_field->form_id = $new_df->id;
                    $new_field->name = $field['name'];
                    $new_field->field_template_id = $field['template'];
                    $new_field->default_value = $field['default_value'];
                    $new_field->is_required = array_get($field, 'is_required', false);
                    $new_field->order = $key + 1;

                    if ($field['view_level'] == "") {
                        $new_field->view_level_id = config('argodf.admin_function_priority');
                    } else {
                        $new_field->view_level_id = $field['view_level'];
                    }

                    if ($field['edit_level'] == "") {
                        $new_field->edit_level_id = config('argodf.admin_function_priority');
                    } else {
                        $new_field->edit_level_id = $field['edit_level'];
                    }

                    if (array_key_exists('options', $field)) {
                        $option_arry = array_map('trim', explode(',', $field['options']));
                        $new_field->options = $option_arry;
                    }

                    $new_field->save();

                    $dynamic_form['fields'][$key]['id'] = $new_field->id;

                    foreach ($dynamic_form['fields'] as $key => $field_item) {
                        if ($field_item['show_if']['field_id'] == $field['id']) {
                            $dynamic_form['fields'][$key]['show_if']['field_id'] = $new_field->id;
                        }
                    }
                }
            }

            foreach ($dynamic_form['fields'] as $key => $field) {
                $update_field_show_if = FormField::find($field['id']);

                if (array_key_exists('show_if', $field)) {
                    if ($field['show_if']['field_id'] != "" and $field['show_if']['equals'] != "") {
                        $update_field_show_if->show_if =
                            array(
                                $field['show_if']['field_id'] => array(
                                    trim($field['show_if']['equals'])
                                )
                            );
                    } else {
                        $update_field_show_if->show_if = null;
                    }
                }

                $update_field_show_if->save();
            }

            DB::commit();

            return redirect(asset('admin/dync_form/'.$new_df->id));
        } catch (Exception $e) {
            DB::rollback();
            abort(400);
        }
    }

    public function show($id)
    {
        $dynamic_form = DynamicForm::GetDynamicFormsForAdmin()
                      ->where('id', '=', $id)
                      ->first();

        foreach ($dynamic_form->fields as $field) {
            if ($field->show_if != "") {
                $show_if_field = array_flatten(array_divide($field->show_if));
                $field->show_if_field_name = FormField::find($show_if_field[0])->name;
                $field->show_if_field_value = $show_if_field[1];
            }
        }

        $array_data = array(
            'dynamic_form' => $dynamic_form
        );

        return view('admin.dync_form.show', $array_data);
    }

    public function edit($id)
    {
        $access_priority = argo_current_permission();

        $dynamic_form = DynamicForm::GetDynamicFormsForAdmin()
                      ->where('id', '=', $id)
                      ->first();

        $pms_levels = PermissionLevel::GetAvailableLevels($access_priority);

        $array_data = array(
            'dynamic_form' => $dynamic_form,
            'field_templates' => FieldTemplate::all(),
            'pms_levels'  => $pms_levels
        );

        return view('admin.dync_form.edit', $array_data);
    }

    public function update(Request $request, $id)
    {
        $df_bsns_logic = new DyncFormBusinessLogic();
        $df_bsns_logic->addFieldRule($request->input('dynamic_form'));

        if (!$df_bsns_logic->isValid($request->input('dynamic_form'), 'update')) {
            return redirect(asset('admin/dync_form/'. $id .'/edit'))
                ->withErrors($df_bsns_logic->getErrors())
                ->withInput($df_bsns_logic->getOriginInput());
        }

        $dynamic_form = $df_bsns_logic->getInput();

        try {
            DB::beginTransaction();

            $update_df = DynamicForm::find($id);
            $update_df->name = $dynamic_form['name'];
            $update_df->is_photo_required = array_get($dynamic_form, 'is_photo_required', false);
            $update_df->save();

            //-- For sync delete fields
            $soft_delete_field_ids = DynamicForm::GetFieldIds($update_df->id);

            if (array_key_exists('fields', $dynamic_form)) {
                foreach ($dynamic_form['fields'] as $key => $field) {
                    $update_field = new FormField;
                    if (substr($field['id'], 0, 3) != 'new') {
                        $update_field = FormField::find($field['id']);
                        //-- Remove not delete field ids
                        unset($soft_delete_field_ids[$field['id']]);
                    }

                    $update_field->form_id = $update_df->id;
                    $update_field->name = $field['name'];
                    $update_field->field_template_id = $field['template'];
                    $update_field->default_value = $field['default_value'];
                    $update_field->is_required = array_get($field, 'is_required', false);
                    $update_field->order = $key + 1;

                    if ($field['view_level'] == "") {
                        $update_field->view_level_id = config('argodf.admin_function_priority');
                    } else {
                        $update_field->view_level_id = $field['view_level'];
                    }

                    if ($field['edit_level'] == "") {
                        $update_field->edit_level_id = config('argodf.admin_function_priority');
                    } else {
                        $update_field->edit_level_id = $field['edit_level'];
                    }

                    if (array_key_exists('options', $field)) {
                        $option_arry = array_map('trim', explode(',', $field['options']));
                        $update_field->options = $option_arry;
                    }

                    $update_field->save();

                    $dynamic_form['fields'][$key]['id'] = $update_field->id;

                    foreach ($dynamic_form['fields'] as $key => $field_item) {
                        if ($field_item['show_if']['field_id'] == $field['id']) {
                            $dynamic_form['fields'][$key]['show_if']['field_id'] = $update_field->id;
                        }
                    }
                }
            }

            foreach ($dynamic_form['fields'] as $key => $field) {
                $update_field_show_if = FormField::find($field['id']);

                if (array_key_exists('show_if', $field)) {
                    if ($field['show_if']['field_id'] != "" and $field['show_if']['equals'] != "") {
                        $update_field_show_if->show_if = array($field['show_if']['field_id'] => array(trim($field['show_if']['equals'])) );
                    } else {
                        $update_field_show_if->show_if = null;
                    }
                }

                $update_field_show_if->save();
            }

            //-- Soft delete fields
            foreach ($soft_delete_field_ids as $key => $field_id) {
                FormField::find($key)->delete();
            }


            DB::commit();

            Log::info("Form ($id) has been update by user " . session()->get('user_id') . " from " . $request->ip());

            return redirect(asset('admin/dync_form/'. $id));
        } catch (Exception $e) {
            Log::error($e);
            DB::rollback();
            abort(400);
        }
    }

    public function destroy($id)
    {
        $dync_form = DynamicForm::findOrFail($id);
        $dync_form->fields()->delete();
        $dync_form->delete();

        Log::warning("Form ($id) has been deleted by user (" . session()->get('user_id') . ") from " . request()->ip());

        return redirect(asset('admin/dync_form'))->with('delete_info', $dync_form);
    }
}
