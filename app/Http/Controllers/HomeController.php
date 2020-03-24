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
        return view('create_property_page');
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
        $l_name = $request->input('l_name');

        if(isset($user) && !is_null($user) && is_numeric($user)) {
            if(isset($address) && !is_null($address) && !empty($address) && isset($suburb) && !is_null($suburb) && !empty($suburb) && isset($postcode) && !is_null($postcode) && !empty($postcode) && is_numeric($postcode) && isset($beds) && !is_null($beds) && !empty($beds) && is_numeric($beds) && isset($baths) && !is_null($baths) && !empty($baths) && is_numeric($baths) && isset($cars) && !is_null($cars) && !empty($cars) && is_numeric($cars) && isset($desc) && !is_null($desc) && !empty($desc) && isset($l_name) && !empty($l_name) && !is_null($l_name)) {

                $insert = ['property_user_id' => $user, 'property_address' => htmlspecialchars($address), 'property_suburb' => htmlspecialchars($suburb), 'property_postcode' => $postcode, 'property_beds' => $beds, 'property_baths' => $baths, 'property_cars' => $cars, 'property_desc' => htmlspecialchars($desc), 'property_title' => $l_name];

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

        $s = strtotime($startDate);
        $e = strtotime($endDate);
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
            if ($r->startDate <= $s && $s < $r->endDate) {
                return json_encode(["status" => "Someone has already booked that time!"]);
            }
        }

        if(isset($userID) && is_numeric($userID) && isset($propertyID) && is_numeric($propertyID) && isset($startDate) && isset($endDate) && isset($persons) && is_numeric($persons) && isset($paid) && is_numeric($paid) && isset($status) && ($status == 1 || $status == 0) ) {
            
            $insert = ['userID' => $userID, 'propertyID' => $propertyID, 'startDate' => $s, 'endDate' => $e, 'persons' => $persons, 'paid' => $paid, 'inactive' => $status];
            
            DB::table('bookings')->insert($insert);
            
            return json_encode(['status' => 'success']);
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

    public function property_reviews(Request $request) {
        $id = Auth::id();

        if(isset($id) && !empty($id) && !is_null($id)) {
            $bookings = DB::table('bookings AS b')
                            ->where([
                                ['b.userID', $id],
                                ['b.inactive', 0],
                                ['b.startDate', '<', time()],
                                ['b.endDate', '<', time()]
                            ])
                            ->join('properties AS p', 'p.property_id', '=', 'b.propertyID')
                            ->get();

            foreach($bookings as $b) {
                $b->startDate = date('d/m/Y', $b->startDate);
                $b->endDate = date('d/m/Y', $b->endDate);
            }

            return view('property_review',
                    [
                        'bookings' => $bookings
                    ]);
        }
        return view('error_page');
    }

    public function tennant_reviews(Request $request) {
        $id = Auth::id();

        if(isset($id) && !empty($id) && !is_null($id)) {
            $bookings = DB::table('bookings AS b')
                            ->where([
                                ['b.inactive', 0],
                                ['b.startDate', '<', time()],
                                ['b.endDate', '<', time()],
                                ['p.property_user_id', $id]
                            ])
                            ->join('properties AS p', 'p.property_id', '=', 'b.propertyID')
                            ->join('users AS u', 'u.id', '=', 'b.userID')
                            ->get();
        
            foreach($bookings as $b) {
                $b->startDate = date('d/m/Y', $b->startDate);
                $b->endDate = date('d/m/Y', $b->endDate);
            }

            return view('tennant_review',
                    [
                        'bookings' => $bookings
                    ]);
        }

        return view('error_page');
    }

    public function create_property_listing(Request $request){
        if($request->isMethod('GET')){
            $id = Auth::id();
            $user_properties = DB::table('properties AS p')
            ->select('p.*')
            ->where([ ['property_user_id', '=', $id] ])
            ->get();
    
            return view('create_property_listing',['properties' => $user_properties]);
        }
        else if($request->isMethod('POST')){
            $user = Auth::id();
            $property = $request->input('property'); //this is property id
            $price = $request->input('price');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');

            if(!isset($price) || !isset($property) || !isset($start_date) || !isset($end_date)){
                return json_encode(['status' => 'bad_input']);
            }

            if($price <=  0){
                return json_encode(['status' => 'price_low']);
            } else if($price >= 1000000){
                return json_encode(['status' => 'price_high']);
            }

            $start = strtotime($start_date);
            $end = strtotime($end_date);
            $curr = time();

            if ($start >= $end || $start <= $curr) {
                return json_encode(['status' => 'date_invalid']);
            }

            $data = ['start_date' => $start, 'end_date' => $end, 'price' => $price, 'property_id' => $property];
            
            DB::table('property_listing')->insert($data);            
            
            return json_encode(['status' => 'success']);
        }
    }
}
