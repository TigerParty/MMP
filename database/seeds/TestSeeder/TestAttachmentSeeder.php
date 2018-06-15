<?php
use Illuminate\Database\Seeder;

class TestAttachmentSeeder extends Seeder {
    public function run() {
        $records = array(
            array(
                'id' => 1,
                'name' => "sample1.jpg",
                'path' => ".test/sample1.jpg",
                'type' => "image/jpeg"
            ),
            array(
                'id' => 2,
                'name' => "sample2.jpg",
                'path' => ".test/sample2.jpg",
                'type' => "image/jpeg"
            ),
            array(
                'id' => 3,
                'name' => "sample3.jpg",
                'path' => ".test/sample3.jpg",
                'type' => "image/png"
            ),
            array(
                'id' => 4,
                'name' => "sample4.jpg",
                'path' => ".test/sample4.jpg",
                'type' => "image/jpeg"
            ),
            array(
                'id' => 5,
                'name' => "hogwarts.png",
                'path' => ".test/hogwarts.png",
                'type' => "image/png"
            ),
            array(
                'id' => 6,
                'name' => "beauxbatons.png",
                'path' => ".test/beauxbatons.png",
                'type' => "image/png"
            ),
            array(
                'id' => 7,
                'name' => "gryffindor.png",
                'path' => ".test/gryffindor.png",
                'type' => "image/png"
            ),
            array(
                'id' => 8,
                'name' => "hufflepuff.png",
                'path' => ".test/hufflepuff.png",
                'type' => "image/png"
            ),
            array(
                'id' => 9,
                'name' => "slytherin.png",
                'path' => ".test/slytherin.png",
                'type' => "image/png"
            ),
            array(
                'id' => 10,
                'name' => "ravenclaw.png",
                'path' => ".test/ravenclaw.png",
                'type' => "image/png"
            ),
            array(
                'id' => 11,
                'name' => "gryffindor-cover.jpg",
                'path' => ".test/gryffindor-cover.jpg",
                'type' => "image/jpg"
            ),
            array(
                'id' => 12,
                'name' => "hufflepuff-cover.jpg",
                'path' => ".test/hufflepuff-cover.jpg",
                'type' => "image/jpg"
            ),
            array(
                'id' => 13,
                'name' => "ravenclaw-cover.jpg",
                'path' => ".test/ravenclaw-cover.jpg",
                'type' => "image/jpg"
            ),
            array(
                'id' => 14,
                'name' => "slytherin-cover.jpg",
                'path' => ".test/slytherin-cover.jpg",
                'type' => "image/jpg"
            ),
            array(
                'id' => 15,
                'name' => "ravenclaw.png",
                'path' => ".test/ravenclaw.png",
                'type' => "image/png"
            ),
            array(
                'id' => 16,
                'name' => "road1.jpg",
                'path' => ".test/road1.jpg",
                'type' => "image/jpg"
            ),
            array(
                'id' => 17,
                'name' => "road2.jpg",
                'path' => ".test/road2.jpg",
                'type' => "image/jpg"
            ),
            array(
                'id' => 18,
                'name' => "road3.jpg",
                'path' => ".test/road3.jpg",
                'type' => "image/jpg"
            ),
            array(
                'id' => 19,
                'name' => "dubai-2003.png",
                'path' => ".test/dubai-2003.png",
                'type' => "image/png"
            ),
            array(
                'id' => 20,
                'name' => "dubai-2004.png",
                'path' => ".test/dubai-2004.png",
                'type' => "image/png"
            ),
            array(
                'id' => 21,
                'name' => "dubai-2005.png",
                'path' => ".test/dubai-2005.png",
                'type' => "image/png"
            ),
            array(
                'id' => 22,
                'name' => "dubai-2006.png",
                'path' => ".test/dubai-2006.png",
                'type' => "image/png"
            ),
            array(
                'id' => 23,
                'name' => "dubai-2007.png",
                'path' => ".test/dubai-2007.png",
                'type' => "image/png"
            ),
            array(
                'id' => 24,
                'name' => "dubai-2008.png",
                'path' => ".test/dubai-2008.png",
                'type' => "image/png"
            ),
            array(
                'id' => 25,
                'name' => "dubai-2009.png",
                'path' => ".test/dubai-2009.png",
                'type' => "image/png"
            ),
            array(
                'id' => 26,
                'name' => "dubai-2010.png",
                'path' => ".test/dubai-2010.png",
                'type' => "image/png"
            ),
            array(
                'id' => 27,
                'name' => "dubai-2011.png",
                'path' => ".test/dubai-2011.png",
                'type' => "image/png"
            ),
            array(
                'id' => 28,
                'name' => "dubai-2012.png",
                'path' => ".test/dubai-2012.png",
                'type' => "image/png"
            ),
            array(
                'id' => 29,
                'name' => "dubai-2013.png",
                'path' => ".test/dubai-2013.png",
                'type' => "image/png"
            ),
            array(
                'id' => 30,
                'name' => "dubai-2014.png",
                'path' => ".test/dubai-2014.png",
                'type' => "image/png"
            ),
            array(
                'id' => 31,
                'name' => "dubai-2015.png",
                'path' => ".test/dubai-2015.png",
                'type' => "image/png"
            ),
            array(
                'id' => 32,
                'name' => "dubai-2016.png",
                'path' => ".test/dubai-2016.png",
                'type' => "image/png"
            ),
            array(
                'id' => 33,
                'name' => 'cover_faculty.png',
                'path' => '.test/cover_faculty.png',
                'type' => 'image/png'
            ),
            array(
                'id' => 34,
                'name' => 'cover_infrastructure.jpg',
                'path' => '.test/cover_infrastructure.jpg',
                'type' => 'image/jpg'
            ),
            array(
                'id' => 35,
                'name' => 'cover_scholarship.jpg',
                'path' => '.test/cover_scholarship.jpg',
                'type' => 'image/jpg'
            ),
            array(
                'id' => 36,
                'name' => 'cover_sm.jpg',
                'path' => '.test/cover_sm.jpg',
                'type' => 'image/jpg'
            ),
            array(
                'id' => 37,
                'name' => 'cover_sppp.jpg',
                'path' => '.test/cover_sppp.jpg',
                'type' => 'image/jpg'
            ),
            array(
                'id' => 38,
                'name' => 'cover_student.jpg',
                'path' => '.test/cover_student.jpg',
                'type' => 'image/jpg'
            ),
            array(
                'id' => 39,
                'name' => 'cover_subsidy.jpg',
                'path' => '.test/cover_subsidy.jpg',
                'type' => 'image/jpg'
            ),
        );
        $this->command->info("attachment : Seeding " . count($records) . " records...");
        foreach($records as $record) {
            DB::table('attachment')->insert($record);
        }
    }
}