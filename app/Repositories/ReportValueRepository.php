<?php

namespace App\Repositories;

use DB;

class ReportValueRepository
{
    public function getValuesOnSaved($report_id)
    {
        return $values = DB::table('report_value')
            ->leftJoin('form_field', 'form_field.id', '=', 'report_value.form_field_id')
            ->where('report_value.report_id', '=', $report_id)
            ->orderBy('form_field.order')
            ->get([
                'form_field.name',
                'report_value.value',
                'form_field.field_template_id',
                'form_field.show_if'
            ]);
    }
}
