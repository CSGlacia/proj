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



    public function book(Request $request) {
        return view("book");
    }

    public function create_booking(Request $request) {
        // Ennumerate variables. Check if the booking is valid.
        // For speed only use relevant variables
        $userID = Auth::id();
        $propertyID = $request->input('propertyID');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $persons = $request->input('persons');
        $paid = $request->input('paid');
        $status = $request->input('status');


        // TODO: (?) User cannot book more than one property for themselves
        // TODO: App crashes if unrecognised commands injected.

        $s = date_create_from_format('Y-m-d', $startDate);
        $e = date_create_from_format('Y-m-d', $endDate);
        // If the end date is before $s, fail. (You can't book for 1 day)
        if ($s >= $e) {
            return json_encode(["status" => "Time failure!"]);
        }


        // Pull from the database.
        $results = DB::table('bookings AS c')
                    ->select('c.*')
                    ->where([ ['propertyID', '=', $propertyID] ])
                    ->get();

        $resultArr = [];


        // If for some propertys' bookings, the END time is STRICTLY GREATER
        // than the start of the booking time.
        foreach ($results as $r) {
            $cStart = date_create_from_format('Y-m-d', $r->startDate);
            $cEnd = date_create_from_format('Y-m-d', $r->endDate);

            if ($cStart <= $s && $s < $cEnd) {
                return json_encode(["status" => "Someone has already booked that time!"]);
            }
        }
        // Ugly if tower. Did it this way for debugging, will clean up later (I have a reminder for this) :D

        if(isset($userID) && is_numeric($userID)) {
            if(isset($propertyID) && is_numeric($propertyID)) {
                if(isset($startDate)) {
                    if(isset($endDate)) {
                        if(isset($persons) && is_numeric($persons)) {
                            if(isset($paid) && is_numeric($paid)) {
                                if(isset($status)){
                                    $insert = ['userID' => $userID, 'propertyID' => $propertyID, 'startDate' => htmlspecialchars($startDate), 'endDate' => htmlspecialchars($endDate), 'persons' => $persons, 'paid' => $paid, 'status' => htmlspecialchars($status)];
                                    DB::table('bookings')->insert($insert);
                                    return json_encode(['status' => 'success']);
                                }
                            }
                        }
                    }
                }
            }
        }

        return json_encode(['status' => 'bad_input']);
    }

    public function get_user_id(Request $request) {
        $id = Auth::id();

        if(isset($id) && !empty($id) && !is_null($id)) {
            return json_encode(['status' => 'success', 'id' => $id]);
        }
        return json_encode(['status' => 'error']);
    }
}
