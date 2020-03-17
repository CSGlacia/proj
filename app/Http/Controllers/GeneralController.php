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
        $query = $request->input('query');
        $address_checkbox = $request->input('address_checkbox');
        $suburb_checkbox = $request->input('suburb_checkbox');
        $postcode_checkbox = $request->input('postcode_checkbox');
        if(empty($query)){
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
        } else{

            $address = true;
            $suburb = true;
            $postcode = true;

            $searchCritera = [];
            if($address_checkbox == 1){
                array_push($searchCritera, ['p.property_address', 'LIKE', '%'.$query.'%', 'OR']);
            }
            if($suburb_checkbox == 1){
                array_push($searchCritera, ['p.property_suburb', 'LIKE', '%'.$query.'%', 'OR']);
            }
            if($postcode_checkbox == 1){
                array_push($searchCritera, ['p.property_postcode', 'LIKE', '%'.$query.'%', 'OR']);
            }
            $results = DB::table('properties AS p')
                            ->select('p.*')
                            ->where([
                                ['property_inactive', '=', '0'],
                                [$searchCritera]
                            ])
                            ->get();

            //dd($results);

            return view('view_properties',
                        ['properties' => $results]
            );
        }
    }
}
