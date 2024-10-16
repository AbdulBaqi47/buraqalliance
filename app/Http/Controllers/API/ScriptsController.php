<?php

namespace App\Http\Controllers\API;

use App\Accounts\Handlers\AccountGateway;
use App\Accounts\Models\Account;
use App\Accounts\Models\Account_transaction;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ScriptsController extends Controller
{
    public function getDatabaseTableList() : array
    {
        $tables = [];
        foreach (DB::getMongoDB()->listCollections() as $collection) {
            $tables[] = $collection->getName() ;
        }

        return $tables;
    }

    public function getDatabaseTableData(Request $request) : array
    {
        # -------------
        #    Paylod
        # -------------
        $tableNames = $request->get('tables', '');
        if(!isset($tableNames) || $tableNames === '') return [];

        # -----------------------------------------
        #   Fetch data against provided tables only
        # -----------------------------------------
        $tableNames = explode(",", $tableNames);
        if(count($tableNames) > 0){

            $tables = [];
            foreach ($tableNames as $tableName) {
                $single_table = [];
                DB::table($tableName)
                ->orderBy('_id')
                ->chunk(500, function (Collection $items) use (&$single_table) {
                    foreach ($items as $item) {
                        $single_table[] = $item;
                    }
                });

                $single_table = collect($single_table)
                ->map(function ($item) {

                    $clonnedItem = collect(Arr::dot($item))
                    ->map(function($value, $key){

                        // dump($value);

                        # -------------------------------
                        # Custom: handles "dates" & "_id"
                        # -------------------------------
                        if($value instanceof \MongoDB\BSON\ObjectId) $value = $value->__toString();
                        if($value instanceof \MongoDB\BSON\UTCDateTime) $value = Carbon::createFromTimestampMs($value->__toString())->toIso8601String();

                        # ------------------------------------
                        # Custom: if url, convert to full url
                        # ------------------------------------
                        if(is_string($value) && preg_match('#^(\w+/)+\w+\.\w+$#', $value) ){ // && Storage::exists($value)
                            # Could be a path
                            $value = Storage::url($value);
                        }

                        return $value;
                    })
                    ->filter(function ($value, $key) {
                        return !is_array($value);
                    })
                    ->toArray();

                    return $clonnedItem;
                })
                ->toArray();

                $tables[$tableName] = $single_table;
            }

            return $tables;
        }

        return ["NO DATA"];
    }
}
