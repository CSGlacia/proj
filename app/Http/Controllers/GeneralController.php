<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

class GeneralController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function view_property(Request $request) {

        $results = DB::table('properties AS p')
                        ->select('p.*')
                        ->where([
                            ['property_inactive', '=', '0']
                        ])
                        ->get(); 
        
        //dd($results);

        return view('view_properties',
                    ['properties' => $results]
    );
    }
   
}
