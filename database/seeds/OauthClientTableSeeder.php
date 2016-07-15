<?php

use Illuminate\Database\Seeder;

class OauthClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       	\DB::table('oauth_clients')->delete();

		\DB::table('oauth_clients')->insert([
			0 => [
		        'id' => '9473560dcc582afb204a669af6eea292',
		        'secret' => '5646db0a17231219febce87ffd4966ac',
		        'name' => 'Main website',
		        'created_at' => '2016-04-05 14:54:13',
		        'updated_at' => '2016-04-06 13:48:05',
		    ],
		    1 =>[
		        'id' => '1da4ad8adc1d073f109e1f113cca12e7',
		        'secret' => 'f28dc0da135e9355a59fcca3cf57dd2e',
		        'name' => 'Ios-v1',
		        'created_at' => '2016-04-05 14:54:13',
		        'updated_at' => '2016-04-06 13:48:05',
			],
		    2 =>[
		        'id' => 'f7dc05d5c6082f9abce7569a5e7b09e0',
		        'secret' => '0e8b52a2032e44febd127bcac95e2b8f',
		        'name' => 'Android-v1',
		        'created_at' => '2016-04-05 14:54:13',
		        'updated_at' => '2016-04-06 13:48:05',
		    ]
		]);
    }
}
