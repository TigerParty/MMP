<?php

namespace App\Services;

use App\Argo\FormField;
use App\Argo\ProjectValue;
use Log;

class FieldFormulaService
{
    /**
     * @var int
     */
    private $projectId;

    /**
     * @var array  key: form_field's id, value: form_field's value
     */
    private $inputFieldValues;

    /**
     * @var array  value: input form_field's id
     */
    private $inputFieldIds;

    /**
     * @var object  form_field's orm
     */
    private $fieldOrm;

    /**
     * @var array  key: form_field's id, value: form_field's formula
     */
    private $formulas;

    /**
     * @var array  value: related form_field's id
     */
    private $relatedFieldIds = array();

    /**
     * @var array  project_value from database  key: form_field's id value: project_value's value
     */
    private $projectValues = array();

    public function __construct()
    {
        $fields = FormField::all(['id', 'formula']);
        $this->fieldOrm = $fields;
    }

    public function calculateFieldValue($projectId, $inputFieldValues)
    {
        if (!is_array($inputFieldValues) || count($inputFieldValues) == 0) {
            Log::warning("Get empty or invalid input.");

            return $inputFieldValues;
        }

        $this->formulas = array_pluck($this->fieldOrm, 'formula', 'id');
        $this->inputFieldValues = $inputFieldValues;
        $this->inputFieldIds = array_keys($this->inputFieldValues);

        //-- Parse formula
        foreach ($this->formulas as $fieldId => &$formula) {
            //-- Replace formula
            if ($formula != null) {
                try {
                    $formula = $this->replaceFormula($fieldId, $formula, []);
                } catch (\Exception $e) {
                    Log::error($e);

                    return $this->inputFieldValues;
                }
            }

            //-- Remove not related field from formulas
            if (!in_array($fieldId, $this->inputFieldIds)) {
                if (!$this->fieldIdExistInFormula($formula)) {
                    array_forget($this->formulas, $fieldId);
                }
            }

            //-- Search field id in formula and push to array
            if ($formula != null) {
                if ($this->fieldIdExistInFormula($formula)) {
                    $fieldIds = $this->getFieldIds($formula);
                    foreach ($fieldIds as $id) {
                        array_push($this->relatedFieldIds, $id);
                    }

                    $this->relatedFieldIds = array_unique($this->relatedFieldIds);
                }
            }
        }

        //-- Get project value from DB
        $projectValues = ProjectValue::where('project_id', '=', $this->projectId)
            ->whereIn('form_field_id', $this->relatedFieldIds)
            ->get(['form_field_id', 'value']);
        $this->projectValues = array_pluck($projectValues, 'value', 'form_field_id');

        //-- Replace field id with value for all formulas, ex: [1]+[2] to 1+2
        foreach ($this->formulas as $fieldId => &$formula) {
            if ($formula == null) {
                $formula = array_get($this->inputFieldValues, $fieldId, null);
            } else {
                $fieldIds = $this->getFieldIds($formula);
                foreach ($fieldIds as $id) {
                    $formula = str_replace("[$id]", $this->getValue($id), $formula);
                }
                $formula = $this->calculateFormula($formula);
            }
        }

        return $this->formulas;
    }

    /**
     * Replace formula with its reference in recursive to get finalized formula
     * @throws Exception  if circular reference detected
     * @return string $formula finalized formula
     */
    private function replaceFormula($fieldId, $formula, $throughIds)
    {
        $fieldIds = $this->getFieldIds($formula);
        foreach ($fieldIds as $id) {
            if (array_get($this->formulas, $id, null)) {
                $replacedFormula = str_replace("[$id]", '(' . $this->formulas[$id] . ')', $formula);
                if (strcmp($formula, $replacedFormula) != 0) {
                    //-- check circular reference
                    if (in_array($id, $throughIds)) {
                        throw new \Exception("Error occurred, circular reference detected for form_field_id $fieldId");
                    }

                    array_push($throughIds, $id);
                    return $this->replaceFormula($fieldId, $replacedFormula, $throughIds);
                }
            }
        }

        return $formula;
    }

    /**
     * Pass formula string and return all field ids in this formula
     * @param string $formula
     * @return array  field ids
     */
    private function getFieldIds($formula)
    {
        $matches = [];
        preg_match_all('/\[[0-9]{1,}\]/', $formula, $matches);
        foreach ($matches[0] as &$id) {
            $id = preg_replace("/\[|\]/", "", $id);
        }
        return $matches[0];
    }

    /**
     * Check input field id exists in formula or not
     * @param string $formula
     * @return boolean  input field id exists in formula or not
     */
    private function fieldIdExistInFormula($formula)
    {
        foreach ($this->inputFieldIds as $inputFieldId) {
            if (str_contains($formula, "[$inputFieldId]")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return value from input field value or db project_value
     * @param int $fieldId
     * @return float
     */
    private function getValue($fieldId)
    {
        return floatval(array_get(
            $this->inputFieldValues,
            $fieldId,
            array_get(
                $this->projectValues,
                $fieldId,
                0
            )
        ));
    }

    /**
     * Calculate string formula by eval() function
     * @param string $formula
     * @return float  calculated value
     */
    private function calculateFormula($formula)
    {
        try {
            return eval("return round($formula, 2);");
        } catch (\Exception $e) {
            Log::error("eval() Exception: " . $e->getMessage());

            return 0;
        } catch (\ParseError $e) {
            Log::error("eval() ParseError: " . $e->getMessage());

            return 0;
        }
    }
}
