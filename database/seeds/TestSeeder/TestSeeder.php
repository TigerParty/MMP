<?php
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TestSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->command->info("Test Seeder Start ...");
        $this->call(TestUserSeeder::class);
        $this->call(TestFormSeeder::class);
        $this->call(TestFormFieldSeeder::class);
        $this->call(TestContainerSeeder::class);
        $this->call(TestAttachmentSeeder::class);
        $this->call(TestAttachablesSeeder::class);
        $this->call(TestProjectSeeder::class);
        $this->call(TestRelationProjectImageContainerSeeder::class);
        $this->call(TestRelationFieldFilterContainer::class);
        $this->call(TestRelationUserOwnProjectSeeder::class);
        $this->call(TestReportSeeder::class);
        $this->call(TestReportValueSeeder::class);
        $this->call(TestRegionLabelSeeder::class);
        $this->call(TestRegionSeeder::class);
        $this->call(TestDynamicConfigSeeder::class);
        $this->call(TestProjectValueSeeder::class);
        $this->call(TestAggregationSeeder::class);
        $this->call(TestIndicatorSeeder::class);
        $this->call(TestCitizenSmsSeeder::class);
        $this->call(TestReportCitizenSeeder::class);
        $this->call(TestSurveySeeder::class);
        $this->command->info("Test Seeder End ...");

        Model::reguard();
    }
}
