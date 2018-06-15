<?php
namespace App\BusinessLogics;

use Validator;
use Exception;

class BusinessLogic
{
	protected $store_validation_rules;
	protected $update_validation_rules;
	protected $input_data;
	protected $validator;

	public function __construct()
	{
		$this->initialRule();
	}

	protected function initialRule()
	{
		//-- Set initial different form Rule here.
	}

	public function isValid($input, $type)
	{
		$this->input_data = $input;

		if ($type == 'store')
		{
			$this->validator = Validator::make(
				$this->input_data,
				$this->store_validation_rules,
				$this->store_validation_messages
			);
			return $this->validator->passes();
		}
		else if ($type == 'update')
		{
			$this->validator = Validator::make(
				$this->input_data,
				$this->update_validation_rules,
				$this->update_validation_messages
			);
			return $this->validator->passes();
		}
		else
		{
			throw new Exception('Unknown validate Type.');
		}
	}

	public function getErrors()
	{
		return $this->validator->errors();
	}

	public function getInput()
	{
		return $this->input_data;
	}

}