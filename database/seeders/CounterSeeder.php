<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        #create a collection 'counter' in central db (only static sequence, start with custom number)

        /**
         * add counters in this array
         *
         */
        $counters = [
            ['ref'=>'invoices', 'seq'=>1000],
            ['ref'=>'account_transactions', 'seq'=>1000],
            // ['ref'=>'drivers', 'seq'=>100],

        ];

        foreach ($counters as $counter) {

            $hasInvoice = DB::getCollection('counters')->findOne(['ref'=>$counter['ref']]);
            if(!isset($hasInvoice)){
                DB::getCollection('counters')->insertOne(
                    ['ref' => $counter['ref'], 'seq'=>$counter['seq']-1]
                );
            }

        }

    }
}
