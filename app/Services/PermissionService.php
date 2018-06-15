<?php

namespace App\Services;

use Auth;
use Log;

class PermissionService
{
    public static function setEditAbleForForm($form)
    {
        $access_priority = argo_current_permission();

        if ($form->edit_level->priority >= $access_priority) {
            $form->edit_able = true;
        } else {
            $form->edit_able = false;
        }

        return $form;
    }

    #-- Set edit able for each form
    public static function setEditAbleForForms($forms)
    {
        $access_priority = argo_current_permission();

        foreach ($forms as $form) {
            if ($form->edit_level->priority >= $access_priority) {
                $form->edit_able = true;
            } else {
                $form->edit_able = false;
            }

            unset($form->edit_level);
        }

        return $forms;
    }
}
