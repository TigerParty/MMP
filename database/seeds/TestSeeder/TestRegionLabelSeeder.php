<?php
use Illuminate\Database\Seeder;

class TestRegionLabelSeeder extends Seeder {
	public function run()
	{
		$records = array(
			array('name' => 'county'),
			array('name' => 'district'),
		);

		\DB::table('region_label')->insert($records);
	}
}