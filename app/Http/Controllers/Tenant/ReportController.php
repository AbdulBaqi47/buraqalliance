<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:tenant_auth');
    }


    /**
     * View Page of Reporting - Generate
     *
    */
    public function ViewReporting(Request $request)
    {

        return view('Tenant.reports.view');
    }

    /**
     * POST request of Reporting - Generate
     *
    */
    public function generate(Request $request)
    {

        $request->validate([
            'type' => 'required|max:255',
            'range' => 'required|max:255',
        ]);


        $type = $request->get('type');
        $range = $request->get('range');
        $start = null;
        $end = null;

        # ------------------------------------
        # Fetch dates according to selection
        # ------------------------------------


        if($range === "date"){
            $start = Carbon::parse($request->get('picker_date'))->format('Y-m-d');
            $end = $start;
        }
        else if($range === "month"){
            $start = Carbon::parse($request->get('picker_month'))->startOfMonth()->format('Y-m-d');
            $end = Carbon::parse($request->get('picker_month'))->endOfMonth()->format('Y-m-d');
        }
        else if($range === "custom"){
            $picker_range_value = explode('-', $request->get('picker_range'));

            $start = Carbon::parse(trim($picker_range_value[0]))->format('Y-m-d');
            $end = Carbon::parse(trim($picker_range_value[1]))->format('Y-m-d');;
        }

        if(!isset($start) || !isset($end)){


            return redirect()->route('tenant.admin.reports.view')
            ->with('error', "Cannot parse dates");

            return;
        }


        # -----------------------------
        # Run Cron to generate report
        # -----------------------------
        $payload = (object)[
            'start' => $start,
            'end' => $end,
            'type' => $type,
            'range' => $range
        ];


        // return $payload;

        if($range === "month"){
            $fileDates = Carbon::parse($start)->format('F Y');
        }
        else{
            $fileDates = "$start - $end";
            if($start === $end) $fileDates = "$start";
        }

        $filePath = "reports/Reports _ $type ($fileDates).xlsx";

        (new ReportExport($payload, $filePath))->queue($filePath, 's3');

        // return Storage::download($filePath);

        return redirect()->route('tenant.admin.reports.view')->with('message', "Report generation is in process!");
    }

}
