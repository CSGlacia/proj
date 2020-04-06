<?php
namespace App\Http\Controllers;

// Include the SDK using the Composer autoloader
require '../vendor/autoload.php';
use \Aws\S3\S3Client;
use \Aws\S3\Exception\S3Exception;

use Illuminate\Http\Request;
use Auth;
use DB;
use App;
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
        $beds = $request->input('beds');
        $baths = $request->input('baths');
        $cars = $request->input('cars');
        $desc = $request->input('desc');
        $l_name = $request->input('l_name');
        $images = $request->file('files');
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        if(isset($user) && !is_null($user) && is_numeric($user)) {
            if(isset($address) && !is_null($address) && !empty($address) && isset($lat) && !is_null($lat) && is_numeric($lat) && !empty($lat) 
            && isset($lng) && !is_null($lng) && !empty($lng) && is_numeric($lng) && isset($beds) && !is_null($beds) && !empty($beds) 
            && is_numeric($beds) && isset($baths) && !is_null($baths) && !empty($baths) && is_numeric($baths) && isset($cars) && !is_null($cars) && !empty($cars) 
            && is_numeric($cars) && isset($desc) && !is_null($desc) && !empty($desc) && isset($l_name) && !empty($l_name) && !is_null($l_name)) {

                if(strpos($address, 'NSW') === false) {
                    return json_encode(['status' => 'wrong_state']);
                }

                $insert = ['property_user_id' => $user, 'property_address' => htmlspecialchars($address), 'property_lat' => $lat, 'property_lng' => $lng, 'property_beds' => $beds, 'property_baths' => $baths, 'property_cars' => $cars, 'property_desc' => htmlspecialchars($desc), 'property_title' => $l_name];

                $property_id = DB::table('properties')
                    ->insertGetId($insert);

                return json_encode(['status' => 'success', 'id' => $property_id]);

            } else {
                return json_encode(['status' => 'bad_input']);
            }
        }

        return json_encode(['status' => 'error']);
    }


    public function upload_property_images(Request $request, $property_id) {
        $images = $request->file();
        $bucket = 'turtle-database';
        $directory = "images/";

        $s3 = new \Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => 'ap-southeast-2'
        ]);

        if(isset($property_id) && !is_null($property_id) && !empty($property_id) && is_numeric($property_id) && isset($images) && !empty($images) && !is_null($images)) {
            foreach ($images['file'] as $key => $value) {
                try {
                    // Upload data.
                    $path = $directory.$property_id.'/'.$key.'.'.$value->extension();
                    $insert = ['property_id' => $property_id,'property_image_name'=> $path];
            
                    DB::table('property_images')
                        ->insert($insert);

                    $result = $s3->putObject(array(
                        'Bucket' => $bucket,
                        'Key'    => $path,
                        'Body'   => $value->get(),
                    ));
                } catch (S3Exception $e) {
                    return json_encode(['status' => 'bad_input']);
                }
            }
            return json_encode(['status' => 'success']);
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
                    ->where([ ['c.booking_propertyID', '=', $propertyID] ])
                    ->get();

        $resultArr = [];


        // If for some propertys' bookings, the END time is STRICTLY GREATER
        // than the start of the booking time.
        foreach ($results as $r) {
            if ($r->booking_startDate <= $s && $s < $r->booking_endDate) {
                return json_encode(["status" => "time_booked"]);
            }
        }

        if(isset($userID) && is_numeric($userID) && isset($propertyID) && is_numeric($propertyID) && isset($startDate) && isset($endDate) && isset($persons) && is_numeric($persons)) {
            
            $insert = ['booking_userID' => $userID, 'booking_propertyID' => $propertyID, 'booking_startDate' => $s, 'booking_endDate' => $e, 'booking_persons' => $persons, 'booking_paid' => 0, 'booking_inactive' => 0];
            
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
                                ['b.booking_userID', $id],
                                ['b.booking_inactive', 0],
                                ['b.booking_startDate', '<', time()],
                                ['b.booking_endDate', '<', time()],
                                ['b.booking_property_reviewed', 0]
                            ])
                            ->join('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                            ->get();


            foreach($bookings as $b) {
                $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                $b->booking_endDate = date('d/m/Y', $b->booking_endDate);
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
            $bookings = DB::table('properties as p')
                            ->where([
                                ['p.property_user_id', $id],
                                ['p.property_inactive', 0],
                                ['b.booking_startDate', '<', time()],
                                ['b.booking_endDate', '<', time()],
                                ['b.booking_tennant_reviewed', 0]
                            ])
                            ->join('bookings AS b', 'b.booking_propertyID', '=', 'p.property_id')
                            ->join('users AS u', 'u.id', '=', 'b.booking_userID')
                            ->get();

            foreach($bookings as $b) {
                $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                $b->booking_endDate = date('d/m/Y', $b->booking_endDate);
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

    public function review_property(Request $request) {
        $booking_id = $request->input('booking_id');
        $property_id = $request->input('prop_id');

        if(isset($booking_id) && !empty($booking_id) && !is_null($booking_id) && is_numeric($booking_id) && isset($property_id) && !empty($property_id) && !is_null($property_id) && is_numeric($property_id)) {

            $property = DB::table('properties AS p')
                            ->where([
                                ['p.property_id', $property_id],
                                ['p.property_inactive', 0]
                            ])
                            ->join('users AS u', 'u.id', '=', 'p.property_user_id')
                            ->first();

            $booking = DB::table('bookings AS b')
                            ->where([
                                ['b.booking_id', $booking_id],
                                ['b.booking_inactive', 0]
                            ])
                            ->first();

            $booking->booking_startDate = date('d/m/Y', $booking->booking_startDate);
            $booking->booking_endDate = date('d/m/Y', $booking->booking_endDate);

            return view('review_property', 
                [
                    'booking_id' => $booking_id,
                    'property_id' => $property_id,
                    'p' => $property,
                    'b' => $booking
                ]
            );
        } else {
            return view('error_page');
        }
    }

    public function review_tennant(Request $request) {
        $booking_id = $request->input('booking_id');
        $property_id = $request->input('prop_id');

        if(isset($booking_id) && !empty($booking_id) && !is_null($booking_id) && is_numeric($booking_id) && isset($property_id) && !empty($property_id) && !is_null($property_id) && is_numeric($property_id)) {

            $property = DB::table('properties AS p')
                            ->where([
                                ['p.property_id', $property_id],
                                ['p.property_inactive', 0]
                            ])
                            ->first();

            $booking = DB::table('bookings AS b')
                            ->where([
                                ['b.booking_id', $booking_id],
                                ['b.booking_inactive', 0]
                            ])
                            ->join('users AS u', 'u.id', '=', 'b.booking_userID')
                            ->first();

            $booking->booking_startDate = date('d/m/Y', $booking->booking_startDate);
            $booking->booking_endDate = date('d/m/Y', $booking->booking_endDate);

            return view('review_tennant', 
                [
                    'booking_id' => $booking_id,
                    'property_id' => $property_id,
                    'p' => $property,
                    'b' => $booking
                ]
            );
        } else {
            return view('error_page');
        }
    }

    public function create_property_review(Request $request) {
        $id = Auth::id();
        $score = $request->input('score');
        $review =$request->input('review');
        $booking_id = $request->input('booking_id');
        $property_id = $request->input('property_id');

        if(isset($id) && !empty($id) && !is_null($id)) {
            if(isset($score) && !empty($score) && !is_null($score) && is_numeric($score) && isset($review) && !is_null($review) && isset($booking_id) && !empty($booking_id) && !is_null($booking_id) && isset($property_id) && !empty($property_id) && !is_null($property_id)) {

                //confirm that the booking exists with the property and user details
                $booking = DB::table('bookings AS b')
                                ->where([
                                    ['b.booking_id', $booking_id],
                                    ['b.booking_propertyID', $property_id],
                                    ['b.booking_userID', $id],
                                    ['b.booking_property_reviewed', 0]
                                ])
                                ->first();

                if(isset($booking) && !empty($booking) && !is_null($booking)) {
                    $insert = ['prs_booking_id' => $booking_id, 'prs_property_id' => $property_id, 'prs_reviewer_id' => $id, 'prs_score' => $score, 'prs_review' => $review, 'prs_submitted_at' => time()];

                    $inserted = DB::table('property_reviews')
                                    ->insertGetId($insert);

                    if(isset($inserted) && !empty($inserted) && !is_null($inserted)) {
                        DB::table('bookings AS b')
                                ->where('b.booking_id', $booking_id)
                                ->update(['b.booking_property_reviewed' => 1]);

                        return json_encode(['status' => 'success']);
                    }
                }
            }

            return json_encode(['status' => 'bad_input']);
        }
        return json_encode(['status' => 'error']);
    }

    public function create_tennant_review(Request $request) {
        $id = Auth::id();
        $score = $request->input('score');
        $review =$request->input('review');
        $booking_id = $request->input('booking_id');
        $tennant_id = $request->input('tennant_id');

        if(isset($id) && !empty($id) && !is_null($id)) {
            if(isset($score) && !empty($score) && !is_null($score) && is_numeric($score) && isset($review) && !is_null($review) && isset($booking_id) && !empty($booking_id) && !is_null($booking_id) && isset($tennant_id) && !empty($tennant_id) && !is_null($tennant_id)) {

                //confirm that the booking exists with the property and user details
                $booking = DB::table('bookings AS b')
                                ->where([
                                    ['b.booking_id', $booking_id],
                                    ['b.booking_userID', $tennant_id],
                                    ['b.booking_tennant_reviewed', 0],
                                    ['p.property_user_id', '=', $id]
                                ])
                                ->join('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                                ->first();

                if(isset($booking) && !empty($booking) && !is_null($booking)) {
                    $insert = ['trs_booking_id' => $booking_id, 'trs_tennant_id' => $tennant_id, 'trs_reviewer_id' => $id, 'trs_score' => $score, 'trs_review' => $review, 'trs_submitted_at' => time()];

                    $inserted = DB::table('tennant_reviews')
                                    ->insertGetId($insert);

                    if(isset($inserted) && !empty($inserted) && !is_null($inserted)) {
                        DB::table('bookings AS b')
                                ->where('b.booking_id', $booking_id)
                                ->update(['b.booking_tennant_reviewed' => 1]);

                        return json_encode(['status' => 'success']);
                    }
                }
            }

            return json_encode(['status' => 'bad_input']);
        }
        return json_encode(['status' => 'error']);
    }

    public function cancel_booking(Request $request) {
        $id = Auth::id();
        $booking_id = $request->input('booking_id');
        $curr = time();

        $booking = DB::table('bookings AS b')
                                ->where([
                                    ['b.booking_id', $booking_id],
                                    ['b.booking_userID', $id],
                                    ['b.booking_property_reviewed', 0]
                                ])
                                ->first();
                                
        $time = strtotime('-14 days', $booking->booking_startDate);

        
        if ($curr <= $time) {
            $changed = DB::table('bookings AS b')
                        ->where([
                            ['b.booking_id', $booking_id],
                            ['b.booking_inactive', 0],

                        ])
                        ->update(['b.booking_inactive' => 1]);
            
            if(!empty($changed)) {
                return json_encode(['status' => 'success']);
            } 
        } else if ($curr > $time) {
            return json_encode(['status' => 'date error']);
        }
        return json_encode(['status' => 'error']);
    }
}
