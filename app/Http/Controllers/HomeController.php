<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function listing_page(Request $request) {
        return view('create_listing_page');
    }

    public function create_property(Request $request) {
        $user = Auth::id();
        $address = $request->input('address');
        $suburb = $request->input('suburb');
        $postcode = $request->input('postcode');
        $beds = $request->input('beds');
        $baths = $request->input('baths');
        $cars = $request->input('cars');
        $desc = $request->input('desc');


        if(isset($user) && !is_null($user) && is_numeric($user)) {
            if(isset($address) && !is_null($address) && !empty($address) && isset($suburb) && !is_null($suburb) && !empty($suburb) && isset($postcode) && !is_null($postcode) && !empty($postcode) && is_numeric($postcode) && isset($beds) && !is_null($beds) && !empty($beds) && is_numeric($beds) && isset($baths) && !is_null($baths) && !empty($baths) && is_numeric($baths) && isset($cars) && !is_null($cars) && !empty($cars) && is_numeric($cars) && isset($desc) && !is_null($desc) && !empty($desc)) {

                $insert = ['property_user_id' => $user, 'property_address' => htmlspecialchars($address), 'property_suburb' => htmlspecialchars($suburb), 'property_postcode' => $postcode, 'property_beds' => $beds, 'property_baths' => $baths, 'property_cars' => $cars, 'property_desc' => htmlspecialchars($desc)];

                DB::table('properties')
                    ->insert($insert);

                return json_encode(['status' => 'success']);

            } else {
                return json_encode(['status' => 'bad_input']);
            }
        }

        return json_encode(['status' => 'error']);
    }
}
