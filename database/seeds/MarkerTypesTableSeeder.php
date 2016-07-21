<?php

use Illuminate\Database\Seeder;

class MarkerTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	
    	\App\Models\MarkerType::firstOrCreate([
			'name'          => 'Featured adventure',
			'description'   => '',
			'marker_points' => 1000,
			'marker_stars'  => 1,
			'marker_for'    => 0
        ]);

        \App\Models\MarkerType::firstOrCreate([
			'name'          => '­Adventure',
			'description'   => '',
			'marker_points' => 500,
			'marker_stars'  => 1,
		]);

        \App\Models\MarkerType::firstOrCreate([
			'name'          => 'Food',
			'description'   => '',
			'marker_points' => 200,
			'marker_stars'  => 1,
		]);

        \App\Models\MarkerType::firstOrCreate([
			'name'          => 'Business/Shop',
			'description'   => '',
			'marker_points' => 200,
			'marker_stars'  => 1,
		 ]);

        \App\Models\MarkerType::firstOrCreate([
			'name'          => 'Event',
			'description'   => '',
			'marker_points' => 1000,
			'marker_stars'  => 1,
        ]);

        \App\Models\MarkerType::firstOrCreate([
            'name'        => 'Treasure Chest',
            'description' => '',
        	'marker_stars' => 1,
        ]);
	}
}
