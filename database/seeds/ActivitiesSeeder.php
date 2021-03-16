<?php

use Illuminate\Database\Seeder;
use App\Activity;

class ActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activity1 = new Activity;
        $activity1->name = "Vacuuming";
        $activity1->duration = 21;
        $activity1->save();

        $activity2 = new Activity;
        $activity2->name = "Window cleaning";
        $activity2->duration = 35;
        $activity2->save();

        $activity3 = new Activity;
        $activity3->name = "Refrigerator cleaning ";
        $activity3->duration = 50;
        $activity3->save();
    }
}
