<?php
use Illuminate\Database\Seeder;

class FieldTemplateSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = array(
            array(
                "id"   => 1,
                "name" => "TextBox",
                "key"  => "text_box",
                "filter_key"  => "text_box",
                "html" => "partials/tp_text_box.html"
            ),
            array(
                "id"   => 2,
                "name" => "TextArea",
                "key"  => "text_area",
                "filter_key"  => "text_box",
                "html" => "partials/tp_text_area.html"
            ),
            array(
                "id"   => 3,
                "name" => "DropDownList",
                "key"  => "drop_down_list",
                "filter_key"  => "drop_down_list",
                "html" => "partials/tp_drop_down_list.html"
            ),
            array(
                "id"   => 4,
                "name" => "CheckBox",
                "key"  => "check_box",
                "filter_key"  => "text_box",
                "html" => "partials/tp_check_box.html"
            ),
            array(
                "id"   => 5,
                "name" => "RadioButton (Conditional)",
                "key"  => "radio_button",
                "filter_key"  => "drop_down_list",
                "html" => "partials/tp_radio_button.html"
            ),
            array(
                "id"   => 6,
                "name" => "Numerical",
                "key"  => "numerical",
                "filter_key"  => "text_box",
                "html" => "partials/tp_numerical.html"
            ),
            array(
                "id"   => 7,
                "name" => "Date",
                "key"  => "date",
                "filter_key"  => "text_box",
                "html" => "partials/tp_date.html"
            ),
            array(
                "id"   => 8,
                "name" => "GPS Tracker",
                "key"  => "gps_tracker",
                "filter_key"  => "text_box",
                "html" => "partials/tp_gps_tracker.html"
            ),
            array(
                "id"   => 9,
                "name" => "CheckBoxGroup",
                "key"  => "check_box_group",
                "filter_key"  => "text_box",
                "html" => "partials/tp_check_box_group.html"
            ),
            array(
                "id"   => 10,
                "name" => "IRI Tracker",
                "key"  => "iri_tracker",
                "filter_key"  => "text_box",
                "html" => "partials/tp_iri_tracker.html"
            )
        );

        $this->command->info("FieldTemplate : Migrating " . count($records) . " FieldTemplate...");
        foreach($records as $record)
        {
            DB::table('field_template')->insert($record);
        }
    }

}
