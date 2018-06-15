<?php
namespace App\Services;

class NavBarService
{
	public static function isActive($tab)
	{
		$request = request();

		if($request->is($tab. "*"))
		{
			return 'active';
		}
		else
		{
			return "";
		}
	}
}
