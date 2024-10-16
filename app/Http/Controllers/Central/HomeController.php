<?php

namespace App\Http\Controllers\Central;

use App\Accounts\Models\Account_log;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Log;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        return view('Central.home');

    }

    public function showProfileForm()
    {

        return view('Central.profile');

    }

    public function save_profile(Request $request)
    {
        $user = Auth::user();

        $validatePayload = [
            'name' => 'required|max:255',

        ];
        if($request->has('change_password')){
            $password = $user->password;
            $validatePayload['password'] = 'required|confirmed|min:8';
            $validatePayload['current_password'] = ['required', function ($attribute, $value, $fail) use ($password) {

                if (!Hash::check($value, $password)) {
                    $fail(__('The current password is incorrect.'));
                }

            }];

            $user->password = Hash::make($request->password);
        }

        $request->validate($validatePayload);

        $user->name = $request->name;
        $user->update();

        return redirect()->route('central.admin.profile')->with('message', "Profile updated successfully!");

    }

    /**
     * Generate blob/binary against path.
     *
     */
    public function getFile(Request $request)
    {

        return Storage::response($request->get('url'));

    }

    public function activity_view(Request $request){
        $config = null;
        if($request->has('id') && $request->has('modal')){
            $config = [];
            $config['id'] = $request->id;
            $config['modal'] = $request->modal;
            $config['category'] = $request->category;
        }
        $models = Log::groupBy('subject_model')->select('subject_model')->get();
        if(isset($config['category']) && $config['category'] !== 'default'){
            $models = Account_log::groupBy('subject_model')->select('subject_model')->get();
        }
        return view('Central.log_activity',compact('models','config'));
    }
}
