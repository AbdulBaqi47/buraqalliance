<?php

namespace App\Traits;

use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait AutoIncreamentTrait
{
    // protected $primaryKey = 'id';
    // protected $keyType = 'integer';

    /**
     * Primary Key $primaryKey
     * @return string
     */
    public function getKeyName()
    {
        return 'id';
    }

    /**
     * Primary Key Type $keyType
     * @return string
     */
    public function getKeyType()
    {
        return 'integer';
    }

    public static function bootAutoIncreamentTrait()
	{
		# we need to set the id to sequence id while creating
        self::creating(function($model){
            $table_name = $model->getTable();
            $model->id = self::getSeq($table_name);
        });
    }

    private static function getSeq($table_name)
    {
        $seq = DB::getCollection('counters')->findOneAndUpdate(
            ['ref' => $table_name],
            ['$inc' => ['seq' => 1]],
            ['new' => true, 'upsert' => true, 'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );
        return $seq->seq;
    }
}
