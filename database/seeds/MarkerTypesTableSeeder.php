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
			'type_slug'     => 'adventure',
			'name'          => 'Adventure',
			'description'   => '',
			'marker_points' => 500,
			'marker_stars'  => 1,
		]);
    	
    	\App\Models\MarkerType::firstOrCreate([
			'name'          => 'Featured adventure',
			'type_slug'     => 'featured-adventure',
			'description'   => '',
			'marker_points' => 1000,
			'marker_stars'  => 1,
			'marker_for'    => 0
        ]);


        \App\Models\MarkerType::firstOrCreate([
			'name'          => 'Food',
			'type_slug'     => 'food',
			'description'   => '',
			'marker_points' => 200,
			'marker_stars'  => 1,
		]);

        \App\Models\MarkerType::firstOrCreate([
			'name'          => 'Business/Shop',
			'type_slug'     => 'business-shop',
			'description'   => '',
			'marker_points' => 200,
			'marker_stars'  => 1,
		 ]);

        \App\Models\MarkerType::firstOrCreate([
			'name'          => 'Event',
			'type_slug'     => 'event',
			'description'   => '',
			'marker_points' => 1000,
			'marker_stars'  => 1,
        ]);

        \App\Models\MarkerType::firstOrCreate([
			'name'         => 'Treasure Chest',
			'type_slug'    => 'treasure-chest',
			'description'  => '',
			'marker_stars' => 1,
        ]);
	}
}
