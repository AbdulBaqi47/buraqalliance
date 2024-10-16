<?php

namespace App\Accounts\Traits;

use Auth;
use Carbon\Carbon;
trait AccountLogsTrait
{
	public static function bootAccountLogsTrait()
	{
		// auto-sets values on creation
        self::created(function($model){
            try {
                $model->model_created($model);
            } catch (\Exception $ex) {
                \Log::channel('single')->error('Account_Log_Activity: in:'.get_class($model).' '.$ex->getMessage());
            }
        });

        // auto-sets values on creation
        self::updating(function ($model) {
            try {
                $model->model_updating($model);
            } catch (\Exception $ex) {
                \Log::channel('single')->error('Account_Log_Activity: in:'.get_class($model).' '.$ex->getMessage());
            }
        });

        self::deleting(function($model){
            try {
                $model->model_deleting($model);
            } catch (\Exception $ex) {
                \Log::channel('single')->error('Account_Log_Activity: in:'.get_class($model).' '.$ex->getMessage());
            }
        });
	}

    #model is created
	public static function model_created($model)
	{
        #get data that was created
        $record = [];
        foreach( $model->toArray() as $key => $value )
        {
            $record[$key]=$value;
        }
        $user_agent = request()->server('HTTP_USER_AGENT');
        $localUtcOffset = request()->cookie('localUtcOffset');
        if(isset($localUtcOffset))$localUtcOffset=request()->cookie('localUtcOffset');
        else $localUtcOffset=000;
        $time = Carbon::now()->utcOffset($localUtcOffset)->toAtomString();
        $ip = $model->getIp();
        $props = [
            'current'=>$record
        ];

        #store the activity
		$log = new \App\Accounts\Models\Account_log;
        $log->description="created";
        $log->subject_id=$model->id;
        $log->subject_model=get_class($model);
        $log->causer_id=Auth::user()->id;
        $log->user_agent=$user_agent;
        $log->ip=$ip;
        $log->props=$props;
        $log->time=$time;
        $log->save();
    }

    // #model is updating
	public static function model_updating($model)
	{
        #get data that was created
        $subject_class=get_class($model);
        $primarykey = '_id';
        if(isset($model->primaryKey))$primarykey=$model->primaryKey;
        try {
            $primarykey=$model->getKeyName();
        } catch (\Throwable $th) { }

        $subject_id=$model[$primarykey];
        $old_modal=$subject_class::find($subject_id)->toArray();

        $currentModel=[];
        $oldModel=[];
        foreach( $model->toArray() as $key => $value )
        {
            if(isset($old_modal[$key])){
                if($old_modal[$key]!=$value){
                    //data changed on this field
                    $currentModel[$key]=$value;
                    $oldModel[$key]=$old_modal[$key];
                }
            }
        }

        $user_agent = request()->server('HTTP_USER_AGENT');
        $ip = $model->getIp();
        $props = [
            'current'=>$currentModel,
            'old'=>$oldModel,
        ];
        $localUtcOffset = request()->cookie('localUtcOffset');
        if(isset($localUtcOffset))$localUtcOffset=request()->cookie('localUtcOffset');
        else $localUtcOffset=000;
        $time = Carbon::now()->utcOffset($localUtcOffset)->toAtomString();

        #store the activity
		$log = new \App\Accounts\Models\Account_log;
        $log->description="updated";
        $log->subject_id=$model->id;
        $log->subject_model=get_class($model);
        $log->causer_id=Auth::user()->id;
        $log->user_agent=$user_agent;
        $log->ip=$ip;
        $log->props=$props;
        $log->time=$time;
        $log->save();


    }

    // #model is deleting
	public static function model_deleting($model)
	{
        #get data that was deleted
        $record = [];
        foreach( $model->toArray() as $key => $value )
        {
            $record[$key]=$value;
        }
        $user_agent = request()->server('HTTP_USER_AGENT');
        $ip = $model->getIp();
        $props = [
            'current'=>$record
        ];
        $localUtcOffset = request()->cookie('localUtcOffset');
        if(isset($localUtcOffset))$localUtcOffset=request()->cookie('localUtcOffset');
        else $localUtcOffset=000;
        $time = Carbon::now()->utcOffset($localUtcOffset)->toAtomString();

        #store the activity
		$log = new \App\Accounts\Models\Account_log;
        $log->description="deleted";
        $log->subject_id=$model->id;
        $log->subject_model=get_class($model);
        $log->causer_id=Auth::user()->id;
        $log->user_agent=$user_agent;
        $log->ip=$ip;
        $log->props=$props;
        $log->time=$time;
        $log->save();
    }

    public static function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return server ip when no client ip found
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany('App\Accounts\Models\Account_log', 'subject_id')->where('subject_model',get_class($this));
    }
}
