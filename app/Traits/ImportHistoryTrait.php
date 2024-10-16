<?php

namespace App\Traits;

use App\Models\Tenant\ImportHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

trait ImportHistoryTrait
{
    /**
     * @var App\Models\Tenant\ImportHistory
     */
    protected $history;

    /**
     * [Import History] Creates a new history object
     *
     * @param string $type Type of history, i.e. vehicle_ledger / income / transaction_ledger / sim
     */
    public function _IH_init($type, $payload = null)
    {
        $localUtcOffset = request()->cookie('localUtcOffset');
        if(isset($localUtcOffset))$localUtcOffset=request()->cookie('localUtcOffset');
        else $localUtcOffset=000;
        $time = Carbon::now()->utcOffset($localUtcOffset)->toAtomString();

        $payload = request()->except(['attachment', '_token', 'DataTables_Table_0_length']);

        # Upload attachment if found
        if(request()->hasFile('attachment')){
            $payload['attachment'] = Storage::putfile('importfiles', request()->file('attachment'));
        }

        $this->history = new ImportHistory;
        $this->history->date = $time;
        $this->history->type = $type;
        $this->history->payload = $payload;
        $this->history->record_relations = [];
        $this->history->total_records = null;
        $this->history->save();
    }

    /**
     * [Import History] Push model to array
     *
     * @param mixed $id
     */
    public function _IH_addRecord($model, $id)
    {
        if(isset($this->history)){
            $this->history->push('record_relations', [
                'model' => $model,
                'id' => $id
            ]);
        }
    }

    /**
     * [Import History] Delete history
     *
     */
    public function _IH_delete()
    {
        if(isset($this->history)){
            $this->history->forceDelete();
        }
    }

    /**
     * [Import History] Updates additional data like total_records & payload
     *
     * @param mixed $id
     */
    public function _IH_addData($payload = [])
    {
        if(isset($this->history)){
            foreach ($payload as $key => $item) {
                $this->history[$key] = $item;
            }
            $this->history->update();
        }
    }

    /**
     * [Import History] Check if no relation found, delete this
     *
     */
    public function _IH_end()
    {
        if(isset($this->history)){
            $count = count($this->history->record_relations);
            if($count === 0){
                $this->_IH_delete();
            }
        }
    }
}
