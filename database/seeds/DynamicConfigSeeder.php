<?php

use Illuminate\Database\Seeder;

class DynamicConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = array(
            array('key' => 'indicator_charts', 'value' => '[]', 'cache_enabled' => 0),
            array('key' => 'header_logo', 'value' => 'images/default_logo.png', 'cache_enabled' => 1),
            array('key' => 'header_title', 'value' => 'Argo Wantok', 'cache_enabled' => 1),
            array('key' => 'header_subtitle', 'value' => 'SMARTER MONITOR - SMARTER CONTRY', 'cache_enabled' => 1),
            array('key' => 'footer_text', 'value' => '["Copyright TigerParty 2014-2016"]', 'cache_enabled' => 1),
            array('key' => 'home_components', 'value' => '[{"key":"banner","value":{"line1":"Track Infrastructure Progress","line2":"Convey Your Feedback","line3":"Interact with Authorities","background":"images\/default_background.jpg"},"path":"partials/home/banner.html"},{"key":"about","value":{"paragraphs":["Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.","In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus.","Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc."]},"path":"partials/home/about.html"},{"key":"project_counter_region","value":[],"path":"partials/home/project_counter_region.html"},{"key":"features","value":[],"path":"partials/home/features.html"}]', 'cache_enabled' => 0),
        );

        $this->command->info("DynamicConfig : Migrating " . count($records) . " DynamicConfig...");
        DB::table('dynamic_config')->insert($records);
    }
}
