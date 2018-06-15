<?php
namespace App\BusinessLogics;

use App\BusinessLogics\BusinessLogic;
use App\Services\PermissionService;
use Log;

use App\Argo\PermissionLevel;
use App\Argo\FieldTemplate;

class DyncFormBusinessLogic extends BusinessLogic
{
    protected function initialRule()
    {
        $this->store_validation_rules = [
            'name' => 'required'
        ];

        $this->update_validation_rules = [
            'name' => 'required'
        ];

        $this->store_validation_messages = [];

        $this->update_validation_messages = [];
    }

    public function addFieldRule($form)
    {
        $user_priority = argo_current_permission();

        if (array_key_exists('fields', $form)) {
            foreach ($form['fields'] as $index => $field) {
                $this->store_validation_rules['fields.'. $index .'.name'] = 'required|max:255';
                $this->store_validation_rules['fields.'. $index .'.template'] = 'required|exists:field_template,id';
                $this->store_validation_rules['fields.'. $index .'.edit_level'] = 'numeric|min:'. $user_priority .'|exists:permission_level,id';
                $this->store_validation_rules['fields.'. $index .'.view_level'] = 'numeric|min:'. $user_priority .'|exists:permission_level,id';
                $this->update_validation_rules['fields.'. $index .'.show_if.equals'] = 'required_with:fields.'. $index .'.show_if.field_id';

                $this->update_validation_rules['fields.'. $index .'.name'] = 'required|max:255';
                $this->update_validation_rules['fields.'. $index .'.template'] = 'required|exists:field_template,id';
                $this->update_validation_rules['fields.'. $index .'.edit_level'] = 'numeric|min:'. $user_priority .'|exists:permission_level,id';
                $this->update_validation_rules['fields.'. $index .'.view_level'] = 'numeric|min:'. $user_priority .'|exists:permission_level,id';
                $this->update_validation_rules['fields.'. $index .'.show_if.equals'] = 'required_with:fields.'. $index .'.show_if.field_id';

                if (array_key_exists('options', $field)) {
                    $this->store_validation_rules['fields.'. $index .'.options'] = 'required|string';
                    $this->update_validation_rules['fields.'. $index .'.options'] = 'required|string';
                }
            }
        }
    }

    public function getOriginInput()
    {
        if (array_key_exists('fields', $this->input_data)) {
            foreach ($this->input_data['fields'] as $index => $field) {
                $this->input_data['fields'][$index]['show_if'] = json_encode(array($this->input_data['fields'][$index]['show_if']['field_id'] => array($this->input_data['fields'][$index]['show_if']['equals'])));
                if (array_key_exists('options', $field)) {
                    $this->input_data['fields'][$index]['options'] = json_encode(explode(',', $this->input_data['fields'][$index]['options']));
                }
                $this->input_data['fields'][$index]['template'] = FieldTemplate::find($this->input_data['fields'][$index]['template']);
                $this->input_data['fields'][$index]['view_level'] = PermissionLevel::select(['id', 'name'])->find($this->input_data['fields'][$index]['view_level']);
                $this->input_data['fields'][$index]['edit_level'] = PermissionLevel::select(['id', 'name'])->find($this->input_data['fields'][$index]['edit_level']);
                $this->input_data['fields'][$index]['default'] = $this->input_data['fields'][$index]['default_value'];
            }
        }

        return $this->input_data;
    }
}
