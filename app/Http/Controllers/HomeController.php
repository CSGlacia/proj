<?php
namespace App\Http\Controllers;

// Include the SDK using the Composer autoloader
require '../vendor/autoload.php';
use \Aws\S3\S3Client;
use \Aws\S3\Exception\S3Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
        $this->middleware(['role:admin|super-admin|user']);
        //block advertisers
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
        $tags = DB::table('tags AS t')
                    ->get();


        $ret_arr = [];

        foreach($tags as $r) {
            $ret_arr[] = ['id' => $r->tag_id, 'text' => $r->tag_name];
        }

        $animals = DB::table('animals AS a')
                    ->get();


        $animalsArray = [];

        foreach($animals as $r) {
            $animalsArray[] = ['id' => $r->animals_id, 'text' => $r->animals_type];
        }


        return view('create_property_page',
            ['tags' => $ret_arr],
            ['animals' => $animalsArray]
        );
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
        $tags = $request->input('tags');
        $animals = $request->input('animals');
        if(isset($user) && !is_null($user) && is_numeric($user)) {
            if(isset($address) && !is_null($address) && !empty($address) && isset($lat) && !is_null($lat) && is_numeric($lat) && !empty($lat)
            && isset($lng) && !is_null($lng) && !empty($lng) && is_numeric($lng) && isset($beds) && !is_null($beds) && !empty($beds)
            && is_numeric($beds) && isset($baths) && !is_null($baths) && !empty($baths) && is_numeric($baths) && isset($cars) && !is_null($cars)
            && is_numeric($cars) && isset($desc) && !is_null($desc) && !empty($desc) && isset($l_name) && !empty($l_name) && !is_null($l_name)
            ) {

                if(strpos($address, 'NSW') === false) {
                    return json_encode(['status' => 'wrong_state']);
                }
                $suburb = explode(',' , $address);
                $suburb = $suburb[1];
                $suburb = explode(' ', $suburb);
                $suburb = $suburb[1];

                $insert = ['property_user_id' => $user, 'property_address' => htmlspecialchars($address), 'property_lat' => $lat, 'property_lng' => $lng, 'property_beds' => $beds, 'property_baths' => $baths, 'property_cars' => $cars, 'property_desc' => htmlspecialchars($desc), 'property_title' => $l_name, 'property_suburb' => $suburb];

                $property_id = DB::table('properties')
                    ->insertGetId($insert);




                $tag_insert = [];
                $ai = [];
                if(isset($tags)){
                    $tags = explode(',', $tags);
                    foreach($tags as $t) {
                        $tag_insert[] = ['pt_property_id' => $property_id, 'pt_tag_id' => $t, 'pt_inactive' => 0];
                    }
                    DB::table('property_tags')
                    ->insert($tag_insert);
                }
                if(isset($animals)){
                    $animals = explode(',', $animals);
                    foreach($animals as $a) {
                        $ai[] = ['property_animals_propertyID' => $property_id, 'property_animals_animalID' => $a, 'property_animals_inactive' => 0];
                    }
                    DB::table('property_animals')
                        ->insert($ai);
                }



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
                        'ACL'    => 'public-read',

                    ));
                } catch (S3Exception $e) {
                    return json_encode(['status' => 'bad_input']);
                }
            }
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'error']);
    }

    public function remove_property_images(Request $request, $property_id) {
        $bucket = 'turtle-database';
        $directory = "images/";
        $remove_ids = $request->input('remove_ids');

        $s3 = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => 'ap-southeast-2'
        ]);

        if(isset($property_id) && !is_null($property_id) && !empty($property_id) && is_numeric($property_id) &&isset($remove_ids) && !is_null($remove_ids) && !empty($remove_ids)) {
            $ids = explode(',', $remove_ids);

            $images = DB::table('property_images')
                        ->where('property_id', $property_id)
                        ->whereIn('image_id', $ids)
                        ->get();

            foreach ($images as $i) {
                try {
                    $result = $s3->deleteObject(array(
                        'Bucket' => $bucket,
                        'Key'    => $i->property_image_name,
                    ));
                } catch (S3Exception $e) {
                    return json_encode(['status' => 'bad_input']);
                }
            }

            DB::table('property_images')
                        ->where('property_id', $property_id)
                        ->whereIn('image_id', $ids)
                        ->delete();
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
        // $animals = $request->input('animals');

        $startDate = explode('/', $startDate);
        $startDate = $startDate[2].'-'.$startDate[1].'-'.$startDate[0];

        $endDate = explode('/', $endDate);
        $endDate = $endDate[2].'-'.$endDate[1].'-'.$endDate[0];

        $s = strtotime($startDate);
        $e = strtotime($endDate);


        // If the end date is before $s, fail. (You can't book for 1 day)
        if ($s >= $e) {
            return json_encode(["status" => "Time failure!"]);
        }

        // Pull from the database.
        $results = DB::table('bookings AS c')
                    ->select('c.*')
                    ->where([
                        ['c.booking_propertyID', '=', $propertyID],
                        ['c.booking_inactive', 0],
                        ['c.booking_approved', 1],
                        ['c.booking_denied', 0]
                    ])
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

            $booking_id = DB::table('bookings')->insertGetId($insert);
            //$this->sendBookingApplicationEmail($propertyID, $s, $e);
            return json_encode(['status' => 'success','id' => $booking_id]);
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
                                ['b.booking_property_reviewed', 0],
                                ['b.booking_denied', 0],
                                ['b.booking_approved', 1]
                            ])
                            ->join('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                            ->join('users AS u', 'u.id', '=', 'p.property_user_id')
                            ->get();


            foreach($bookings as $b) {
                $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                $b->booking_endDate = date('d/m/Y', $b->booking_endDate);
            }

            $reviews = DB::table('property_reviews AS r')
                            ->where([
                                ['r.prs_reviewer_id', $id],
                                ['r.prs_inactive', 0]
                            ])
                            ->join('properties AS p', 'p.property_id', '=', 'r.prs_property_id')
                            ->join('users AS u', 'u.id', '=', 'p.property_user_id')
                            ->join('bookings AS b', 'b.booking_id', '=', 'r.prs_booking_id')
                            ->get();

            foreach($reviews as $r) {
                $r->booking_startDate = date('d/m/Y', $r->booking_startDate);
                $r->booking_endDate = date('d/m/Y', $r->booking_endDate);
            }

            return view('property_review',
                    [
                        'bookings' => $bookings,
                        'reviews' => $reviews
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
                                ['b.booking_tennant_reviewed', 0],
                                ['b.booking_denied', 0],
                                ['b.booking_approved', 1]
                            ])
                            ->join('bookings AS b', 'b.booking_propertyID', '=', 'p.property_id')
                            ->join('users AS u', 'u.id', '=', 'b.booking_userID')
                            ->get();

            foreach($bookings as $b) {
                $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                $b->booking_endDate = date('d/m/Y', $b->booking_endDate);
            }

            $past_reviews = DB::table('tennant_reviews as t')
                            ->where([
                                ['t.trs_inactive', 0],
                                ['t.trs_reviewer_id', $id]
                            ])
                            ->join('bookings AS b', 'b.booking_id', '=', 't.trs_booking_id')
                            ->join('users AS u', 'u.id', '=', 'b.booking_userID')
                            ->join('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                            ->get();

            foreach($past_reviews as $p) {
                $p->trs_submitted_at = date('d/m/Y', $p->trs_submitted_at);
                $p->booking_startDate = date('d/m/Y', $p->booking_startDate);
                $p->booking_endDate = date('d/m/Y', $p->booking_endDate);
            }

            return view('tennant_review',
                    [
                        'bookings' => $bookings,
                        'past_reviews' => $past_reviews
                    ]);
        }

        return view('error_page');
    }

    public function create_property_listing(Request $request){
        if($request->isMethod('GET')){
            $id = Auth::id();
            $user_properties = DB::table('properties AS p')
            ->select('p.*')
            ->where([ ['property_user_id', '=', $id],
                        ['property_inactive',0] ])
            ->get();

            return view('create_property_listing',['properties' => $user_properties]);
        }
        else if($request->isMethod('POST')){
            $user = Auth::id();
            $property = $request->input('property'); //this is property id
            $price = $request->input('price');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $reccurring = $request->input('recurr');

            if(!isset($price) || !isset($property) || !isset($start_date) || !isset($end_date)){
                return json_encode(['status' => 'bad_input']);
            }

            if($price <=  0){
                return json_encode(['status' => 'price_low']);
            } else if($price >= 1000000){
                return json_encode(['status' => 'price_high']);
            }
            if(isset($reccurring)){
                if($reccurring != "false" && $reccurring != "true") {
                    return json_encode(['status' => 'error']);
                }

                if($reccurring == "false") {
                    $reccurring = 0;
                }

                if($reccurring == "true") {
                    $reccurring = 1;
                }
            }
            else{
                $reccurring = 0;
            }

            $start_date = explode('/', $start_date);
            $start_date = $start_date[2].'-'.$start_date[1].'-'.$start_date[0];

            $end_date = explode('/', $end_date);
            $end_date = $end_date[2].'-'.$end_date[1].'-'.$end_date[0];

            $start = strtotime($start_date);
            $end = strtotime($end_date);
            $curr = time();

            if ($start > $end || $start < $curr) {
                return json_encode(['status' => 'date_invalid']);
            }
            if ($this->checkValidDates($start, $end, $property) == false){
                return json_encode(['status' => 'overlapping_date']);
            }

            $data = ['start_date' => $start, 'end_date' => $end, 'price' => $price, 'property_id' => $property, 'reccurring' => $reccurring];

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
                                ['b.booking_inactive', 0],
                                ['b.booking_property_reviewed', 0]
                            ])
                            ->first();

            if(isset($booking) && !empty($booking) && !is_null($booking)) {

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
                return view('bad_permissions');
            }
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
                                ['b.booking_inactive', 0],
                                ['b.booking_tennant_reviewed', 0]
                            ])
                            ->join('users AS u', 'u.id', '=', 'b.booking_userID')
                            ->first();

            if(isset($booking) && !empty($booking) && !is_null($booking)) {


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
                return view('bad_permissions');
            }
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

    public function edit_property(Request $request, $id) {
        $user_id = Auth::id();

        if(isset($id) && !empty($id) && !is_null($id)) {

            $prop = DB::table('properties AS p')
                        ->select('p.*',
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.tag_id) SEPARATOR ",") FROM property_tags AS pt LEFT JOIN tags as t ON t.tag_id = pt.pt_tag_id WHERE pt.pt_property_id = p.property_id AND pt.pt_inactive = 0) AS `tags`'),
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(a.animals_type) SEPARATOR ",") FROM property_animals AS pa LEFT JOIN animals as a ON a.animals_type = pa.property_animals_animalID WHERE pa.property_animals_propertyID = p.property_id AND pa.property_animals_inactive = 0) AS `animals`')
                        )
                        ->where([
                            ['p.property_id', $id],
                            ['p.property_inactive', 0]
                        ])
                        ->first();

            $selected_tags = explode(',' , $prop->tags);
            $selected_animals = explode(',', $prop->animals);

            if($user_id != $prop->property_user_id) {
                return view('bad_permissions');
            }

            $listings = DB::table('property_listing AS pl')
                            ->where([
                                ['pl.property_id', $id],
                                ['pl.inactive', 0]
                            ])
                            ->get();

            foreach($listings as $l) {
                $l->start_date = date('d/m/Y', $l->start_date);
                $l->end_date = date('d/m/Y', $l->end_date);
            }

            $bucket = 'turtle-database';

            $s3 = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => 'ap-southeast-2'
            ]);

            $prop_images = DB::table('property_images AS p')
                            ->select('p.*')
                            ->where([['p.property_id',$id]])
                            ->get();

            $tags = DB::table('tags AS t')
                        ->get();


            $tag_ret_arr = [];

            foreach($tags as $r) {
                if(in_array($r->tag_id, $selected_tags)) {
                    $is_selected = true;
                } else {
                    $is_selected = false;
                }

                $tag_ret_arr[] = ['id' => $r->tag_id, 'text' => $r->tag_name, 'selected' => $is_selected];
            }

            $animals = DB::table('animals AS a')
                        ->get();


            $ai = [];

            foreach($animals as $r) {

                if(in_array($r->animals_type, $selected_animals)) {
                    $is_selected = true;
                } else {
                    $is_selected = false;
                }

                $ai[] = ['id' => $r->animals_id, 'text' => $r->animals_type, 'selected' => $is_selected];
            }


            return view('edit_property',
                            ['p' => $prop,
                            'images' => $prop_images,
                            'listings' => $listings,
                            'image_count' => count($prop_images),
                            'tags' => $tag_ret_arr,
                            'animals' => $ai
                            ]
                );
        }
    }

    public function update_property(Request $request) {
        $prop_id = $request->input('prop_id');
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
        $tags = $request->input('tags');
        $animals = $request->input('animals');

        if(isset($user) && !is_null($user) && is_numeric($user)) {
            if(isset($address) && !is_null($address) && !empty($address) && isset($lat) && !is_null($lat) && is_numeric($lat) && !empty($lat)
            && isset($lng) && !is_null($lng) && !empty($lng) && is_numeric($lng) && isset($beds) && !is_null($beds)
            && is_numeric($beds) && isset($baths) && !is_null($baths) && !empty($baths) && is_numeric($baths) && isset($cars) && !is_null($cars)
            && is_numeric($cars) && isset($desc) && !is_null($desc) && !empty($desc) && isset($l_name) && !empty($l_name) && !is_null($l_name)) {

                if(strpos($address, 'NSW') === false) {
                    return json_encode(['status' => 'wrong_state']);
                }

                $suburb = explode(',' , $address);
                $suburb = $suburb[1];
                $suburb = explode(' ', $suburb);
                $suburb = $suburb[1];

                $update = ['property_user_id' => $user, 'property_address' => htmlspecialchars($address), 'property_lat' => $lat, 'property_lng' => $lng, 'property_beds' => $beds, 'property_baths' => $baths, 'property_cars' => $cars, 'property_desc' => htmlspecialchars($desc), 'property_title' => $l_name, 'property_suburb' => $suburb];

                DB::table('properties')
                    ->where('property_id', $prop_id)
                    ->update($update);

                $tags = explode(',', $tags);

                DB::table('property_tags')
                        ->where([
                            ['pt_property_id', $prop_id],
                            ['pt_inactive', 0]
                        ])
                        ->delete();

                $tag_insert = [];

                foreach($tags as $t) {
                    $tag_insert[] = ['pt_property_id' => $prop_id, 'pt_tag_id' => (int)$t, 'pt_inactive' => 0];
                }

                DB::table('property_tags')
                    ->insert($tag_insert);

                // Handle animals.
                $animals = explode(',', $animals);

                DB::table('property_animals')
                        ->where([
                            ['property_animals_propertyID', $prop_id],
                            ['property_animals_inactive', 0]
                        ])
                        ->delete();

                $ai = [];

                foreach($animals as $a) {
                    $ai[] = ['property_animals_propertyID' => $prop_id, 'property_animals_animalID' => (int)$a, 'property_animals_inactive' => 0];
                }

                DB::table('property_animals')
                    ->insert($ai);



                return json_encode(['status' => 'success']);

            } else {
                return json_encode(['status' => 'bad_input']);
            }
        }

        return json_encode(['status' => 'error']);
    }

    public function update_property_listing(Request $request) {
        $user = Auth::id();
        $property = $request->input('property'); //this is property id
        $price = $request->input('price');
        $data = $request->input('data');
        if($data == "~~false"){
            return json_encode(['status' => 'bad_input']);
        }
        //set current property listings to inactive
        DB::table('property_listing')
            ->where([
                ['inactive', 0],
                ['property_id', $property]
            ])
            ->update(['inactive' => 1]);

        $data = explode(',', $data);
        $listings = [];

        foreach($data as $d) {
            $listings[] = explode('~', $d);
        }

        if(!isset($price) || !isset($property)){
            return json_encode(['status' => 'bad_input']);
        }

        if($price <=  0){
            return json_encode(['status' => 'price_low']);
        } else if($price >= 1000000){
            return json_encode(['status' => 'price_high']);
        }

        $insert = [];

        foreach($listings as $l) {
            $start = explode('/', $l[0]);
            $start = $start[2].'-'.$start[1].'-'.$start[0];

            $end = explode('/', $l[1]);
            $end = $end[2].'-'.$end[1].'-'.$end[0];

            $start = strtotime($start);
            $end = strtotime($end);
            $curr = time();

            if($l[2] == "false") {
                $reccurring = 0;
            } else {
                $reccurring = 1;
            }

            if ($start >= $end || $start <= $curr) {
                return json_encode(['status' => 'date_invalid']);
            }

            $insert = ['start_date' => $start, 'end_date' => $end, 'price' => $price, 'property_id' => $property, 'reccurring' => $reccurring];
        }
        DB::table('property_listing')->insert($insert);


        return json_encode(['status' => 'success']);
    }

    public function add_to_wishlist(Request $request) {
        $userID = Auth::id();
        $propertyID = $request->input('propertyID');
        $propertyTitle = $request->input('propertyTitle');
        $propertyAddress = $request->input('propertyAddress');
        $createdAt = time();


        // Pull from the database.
        $results = DB::table('wishlist AS w')
                    ->select('w.*')
                    ->where([
                        ['wishlist_propertyID', '=', $propertyID],
                        ['wishlist_userID', '=', $userID],
                        ['wishlist_inactive',0]
                    ])
                    ->first();
        if(isset($results)){
            return json_encode(['status' => 'exists']);
        }
        DB::table('wishlist')
                ->updateOrInsert(
                        ['wishlist_userID' => $userID, 'wishlist_propertyID' => $propertyID],
                        ['wishlist_propertyTitle' => $propertyTitle, 'wishlist_propertyAddress' => $propertyAddress, 'wishlist_inactive' => 0, 'wishlist_createdAt' => $createdAt]
                );

        // Sanity.

        // Only add to the database if it does not exist on there.
        // if (count($results) < 1) {
        //     error_log("SUP3");
        //     $insert = ['wishlist_userID' => $userID, 'wishlist_propertyID' => $propertyID, 'wishlist_propertyTitle' => $propertyTitle, 'wishlist_propertyAddress' => $propertyAddress, 'wishlist_inactive' => 0];
        //     DB::table('wishlist')->insert($insert);
        // } else {
        //     DB::table('wishlist')
        //         ->where('id')
        // }

        return json_encode(['status' => 'success']);

    }

    public function view_wishlist(Request $request) {

        $userID = Auth::id();

        $results = DB::table('wishlist AS w')
                        ->select('w.*')
                        ->where([
                            ['wishlist_inactive', '=', '0'],
                            ['wishlist_userID', '=', $userID]
                        ])
                        ->get();

        //

        return view('view_wishlist', ['wishlist' => $results]);
    }

    public function delete_wishlist(Request $request) {
        $userID = Auth::id();
        $propertyID = $request->input('propertyID');


        DB::table('wishlist')
                ->updateOrInsert(
                        ['wishlist_userID' => $userID, 'wishlist_propertyID' => $propertyID],
                        ['wishlist_inactive' => 1]
                );

        return json_encode(['status' => 'success']);
    }

    public function delete_property(Request $request) {

        $userID = Auth::id();
        $id = $request->input('propertyID');

        $results = DB::table('properties')
                    ->where([
                        ['property_user_id', '=', $userID],
                        ['property_id', '=', $id],
                    ])
                    ->get();

        if (!empty($results)) {
        DB::table('properties')
            ->where([
                ['property_user_id', '=', $userID],
                ['property_id', '=', $id],
            ])
            ->update(['property_inactive' => 1]);

            return json_encode(["status" => "success"]);
        }
        return json_encode(["status" => "property_remove_error"]);
    }

    public function edit_tennant_review(Request $request, $review_id) {
        $id = Auth::id();

        if(isset($id) && !is_null($id) && !empty($id) && isset($review_id) && !is_null($review_id) && !empty($review_id)) {
            $review = DB::table('tennant_reviews AS t')
                        ->where([
                            ['t.trs_id', $review_id],
                            ['t.trs_reviewer_id', $id],
                            ['t.trs_inactive', 0]
                        ])
                        ->join('bookings AS b', 'b.booking_id', '=', 't.trs_booking_id')
                        ->join('users AS u', 'u.id', '=', 'b.booking_userID')
                        ->join('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                        ->first();

            $review->booking_startDate = date('d/m/Y', $review->booking_startDate);
            $review->booking_endDate = date('d/m/Y', $review->booking_endDate);

                return view('edit_tennant_review',
                    ['review' => $review]
                );

        }

        return view('bad_permissions');
    }

    public function update_tennant_review(Request $request) {
        $id = Auth::id();
        $score = $request->input('score');
        $review = $request->input('review');
        $review_id = $request->input('review_id');

        if(isset($id) && !is_null($id) && !empty($id)) {
            if(isset($score) && !empty($score) && !is_null($score) && is_numeric($score) && isset($review) && !is_null($review)) {

                $review_present = DB::table('tennant_reviews')
                            ->where('trs_id', $review_id)
                            ->first();

                if(isset($review_present) && !is_null($review_present)) {
                    $insert = ['trs_score' => $score, 'trs_review' => $review, 'trs_edited_at' => time(), 'trs_edited' => 1];

                    $inserted = DB::table('tennant_reviews')
                                    ->where('trs_id', $review_id)
                                    ->update($insert);

                    return json_encode(['status' => 'success']);
                }
            }
            return json_encode(['status' => 'bad_input']);
        }
        return json_encode(['status' => 'error']);
    }

    public function edit_property_review(Request $request, $review_id) {
        $id = Auth::id();

        if(isset($review_id) && !empty($review_id) && !is_null($review_id) && isset($id) && !empty($id) && !is_null($id)) {
            $review = DB::table('property_reviews AS r')
                        ->where([
                            ['r.prs_reviewer_id', $id],
                            ['r.prs_id', $review_id]
                        ])
                        ->join('properties AS p', 'p.property_id', '=', 'r.prs_property_id')
                        ->join('bookings AS b', 'b.booking_id', '=', 'r.prs_booking_id')
                        ->join('users AS u', 'u.id', '=', 'p.property_user_id')
                        ->first();

            $review->booking_startDate = date('d/m/Y', $review->booking_startDate);
            $review->booking_endDate = date('d/m/Y', $review->booking_endDate);

            return view('edit_property_review',
                [
                    'review' => $review
                ]
            );
        }
        return view('bad_permissions');
    }

    public function update_property_review(Request $request) {
        $id = Auth::id();
        $score = $request->input('score');
        $review = $request->input('review');
        $review_id = $request->input('review_id');

        if(isset($id) && !is_null($id) && !empty($id)) {
            if(isset($score) && !empty($score) && !is_null($score) && is_numeric($score) && isset($review) && !is_null($review)) {

                $review_present = DB::table('property_reviews')
                            ->where('prs_id', $review_id)
                            ->first();

                if(isset($review_present) && !is_null($review_present)) {
                    $insert = ['prs_score' => $score, 'prs_review' => $review, 'prs_edited_at' => time(), 'prs_edited' => 1];

                    $inserted = DB::table('property_reviews')
                                    ->where('prs_id', $review_id)
                                    ->update($insert);

                    return json_encode(['status' => 'success']);
                }
            }
            return json_encode(['status' => 'bad_input']);
        }
        return json_encode(['status' => 'error']);
    }

    /* Email stuff when logged in*/
    public static function sendBookingApplicationEmail($propertyID, $startDate, $endDate)
    {
        $userEmail = DB::table('users AS u')
                    ->select('email')
                    ->where([ ['u.id', '=', Auth::id()], ])
                    ->first();
        $propOwnerID = DB::table('properties AS p')
                    ->select('property_user_id')
                    ->where([ ['p.property_id', '=', $propertyID], ])
                    ->first();
        $hostEmail = DB::table('users AS u')
                    ->select('email')
                    ->where([ ['u.id', '=', $propOwnerID->property_user_id], ])
                    ->first();

        $propName = DB::table('properties AS p')
                    ->select('property_title')
                    ->where([ ['p.property_id', '=', $propertyID], ])
                    ->first();

        $startDateStr = date("Y-m-d", $startDate);
        $endDateStr = date("Y-m-d", $endDate);
        $data = array('email' => $userEmail->email, 'propName' => $propName->property_title, 'startDate' => $startDateStr, 'endDate' => $endDateStr);
        $hostData = array('email' => $hostEmail->email, 'propName' => $propName->property_title, 'startDate' => $startDateStr, 'endDate' => $endDateStr);
        /*Mail::send('emails.booking_application', $data, function ($message) use ($userEmail)
        {
            $message->from('turtleaccommodation@gmail.com', 'TurtleTeam');
            $message->to($userEmail->email);
        });
        Mail::send('emails.host_booking_application', $hostData, function ($message) use ($hostEmail)
        {
            $message->from('turtleaccommodation@gmail.com', 'TurtleTeam');
            $message->to($hostEmail->email);
        });*/
    }

    public static function sendBookingStatusEmail($bookingID, $status)
    {

        $booking = DB::table('bookings AS b')
                    ->where([ ['b.booking_id', '=', $bookingID], ])
                    ->first();
        $userEmail = DB::table('users AS u')
                    ->select('email')
                    ->where([ ['u.id', '=', $booking->booking_userID], ])
                    ->first();
        $propName = DB::table('properties AS p')
                    ->select('property_title')
                    ->where([ ['p.property_id', '=', $booking->booking_propertyID], ])
                    ->first();
        $startDateStr = date("Y-m-d", $booking->booking_startDate);
        $endDateStr = date("Y-m-d", $booking->booking_endDate);
        $data = array('email' => $userEmail->email, 'propName' => $propName->property_title, 'startDate' => $startDateStr, 'endDate' => $endDateStr);
        $emailForm = '';
        if ($status == 'denied'){
            $emailForm = 'emails.booking_denied';
        } else if ($status == 'approved'){
            $emailForm = 'emails.booking_approved';
        }

        /*Mail::send($emailForm, $data, function ($message) use ($userEmail)
        {
            $message->from('turtleaccommodation@gmail.com', 'TurtleTeam');
            $message->to($userEmail->email);
        });*/

    }

    /* Comparing 2 start and end dates to check if they overlap */
    public function checkValidDates($startDate1, $endDate1, $prop_id)
    {
        $startDateNoYear1 = $startDate1 % 31622400;
        $endDateNoYear1 = $endDate1 % 31622400;
        $prop_listsings = DB::table('property_listing AS p')
                    ->select('start_date', 'end_date', 'reccurring')
                    ->where([
                        ['p.property_id', '=', $prop_id],
                        ['p.inactive', '=', '0']
                    ])
                    ->get();
        foreach ($prop_listsings as $p) {
            if ($p->reccurring == 1){
                $startDateNoYear2 = $p->start_date % 31622400;
                $endDateNoYear2 = $p->end_date % 31622400;
                if ($startDateNoYear1 == $startDateNoYear2){
                    return false;
                } else if ($startDateNoYear1 < $startDateNoYear2) {
                    if ($startDateNoYear2 <= $endDateNoYear1) {
                        return false;
                    }
                } else {
                    if ($startDateNoYear1 <= $endDateNoYear2){
                        return false;
                    }
                }
            } else{
                $startDate2 = $p->start_date;
                $endDate2 = $p->end_date;
                if ($startDate1 == $startDate2){
                    return false;
                } else if ($startDate1 < $startDate2) {
                    if ($startDate2 <= $endDate1) {
                        return false;
                    }
                } else {
                    if ($startDate1 <= $endDate2){
                        return false;
                    }
                }
            }
        }
        return true;

    }

    public function admin_test(Request $request){
        Auth::user()->assignRole('super-admin');
        return Auth::user()->roles;
    }

    public function view_booking(Request $request, $booking_id) {
        $id = Auth::id();

        if(isset($booking_id) && !empty($booking_id) && !is_null($booking_id)) {
            $booking = DB::table('bookings AS b')
                            ->select('b.*', 'p.*', 'u.*',
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = b.booking_propertyID) AS `scores`'),
                                DB::raw('(SELECT COUNT(r.prs_score) FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = b.booking_propertyID) AS `review_count`')
                            )
                            ->where('b.booking_id', $booking_id)
                            ->join('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                            ->join('users AS u', 'u.id', '=', 'b.booking_userID')
                            ->get();

            if(count($booking) == 1) {
                $booking = $booking[0];

                if($booking->booking_userID == $id || $booking->property_user_id == $id) {


                    if(isset($booking) && !empty($booking) && !is_null($booking)) {
                        $past_check = $booking->booking_endDate;
                        $booking->booking_startDate = date('d/m/Y', $booking->booking_startDate);
                        $booking->booking_endDate = date('d/m/Y', $booking->booking_endDate);

                        if(isset($booking->scores) && !empty($booking->scores) && !is_null($booking->scores) && isset($booking->review_count) && !empty($booking->review_count) && !is_null($booking->review_count)) {
                            $booking->scores = $booking->scores/$booking->review_count;
                        } else {
                            $booking->scores = -1;
                        }

                        $status = "NOT APPROVED";

                        if($booking->booking_approved == 1) {
                            $status = "APPROVED";
                        }

                        if($past_check < time()) {
                            $status = "FINISHED";
                        }

                        if($booking->booking_denied == 1) {
                            $status = "DENIED";
                        }

                        $prop_owner_name = DB::table('users')
                                        ->select('name')
                                        ->where('id',$booking->property_user_id)
                                        ->first();

                        return view('view_booking', [
                            'b' => $booking,
                            'status' => $status,
                            'user_id' => $id,
                            'prop_owner_name' => $prop_owner_name,
                        ]);
                    }
                } else {
                    return view('bad_permissions');
                }
            }
        }

        return view('error_page');
    }

    public function approve_booking(Request $request, $booking_id) {
        $id = Auth::id();

        if(isset($booking_id) && !empty($booking_id) && !is_null($booking_id)) {
            $booking = DB::table('bookings AS b')
                            ->where([
                                ['b.booking_id', $booking_id],
                                ['b.booking_approved', 0],
                                ['b.booking_denied', 0],
                                ['p.property_user_id', $id]
                            ])
                            ->join('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                            ->first();

            if(isset($booking) && !empty($booking) && !is_null($booking)) {
                $check_bookings = DB::table('bookings AS b')
                                    ->where([
                                        ['b.booking_approved', 1],
                                        ['b.booking_inactive', 0],
                                        ['b.booking_denied', 0],
                                        ['b.booking_propertyID', $booking->booking_propertyID],
                                        ['b.booking_startDate', '>', time()]
                                    ])
                                    ->get();

                $booking_overlap = false;

                foreach($check_bookings as $cb) {
                    if($booking->booking_startDate <= $cb->booking_endDate && $booking->booking_endDate >= $cb->booking_startDate) {
                        $booking_overlap = true;
                    }
                }

                if($booking_overlap == true) {
                    //have to deny booking cause of overlap
                    DB::table('bookings AS b')
                        ->where([
                            ['b.booking_id', $booking_id],
                            ['b.booking_approved', 0],
                            ['b.booking_inactive', 0]
                        ])
                        ->update(['booking_denied' => 1]);
                    //$this->sendBookingStatusEmail($booking_id, 'denied');
                    return json_encode(['status' => 'overlapping_bookings']);

                } else {
                    //approve booking
                    DB::table('bookings AS b')
                        ->where([
                            ['b.booking_id', $booking_id],
                            ['b.booking_approved', 0],
                            ['b.booking_inactive', 0]
                        ])
                        ->update(['booking_approved' => 1]);
                    //$this->sendBookingStatusEmail($booking_id, 'approved');
                    return json_encode(['status' => 'success']);

                }
            } else {
                return json_encode(['status' => 'error']);
            }
        }
    }

    public function deny_booking(Request $request, $booking_id) {
        $id = Auth::id();

        if(isset($booking_id) && !empty($booking_id) && !is_null($booking_id)) {

            $booking = DB::table('bookings AS b')
                            ->where([
                                ['b.booking_id', $booking_id],
                                ['b.booking_approved', 0],
                                ['p.property_user_id', $id]
                            ])
                            ->join('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                            ->first();

            if(isset($booking) && !empty($booking) && !is_null($booking)) {

                DB::table('bookings AS b')
                    ->where([
                        ['b.booking_id', $booking_id],
                        ['b.booking_approved', 0],
                        ['b.booking_inactive', 0]
                    ])
                    ->update(['booking_denied' => 1]);
                    //$this->sendBookingStatusEmail($booking_id, 'denied');
                return json_encode(['status' => 'success']);

            }
        }

        return json_encode(['status' => 'error']);
    }

    public function view_user(Request $request, $id) {
        if(isset($id) && !is_null($id) && !empty($id) && is_numeric($id)) {
            $results = DB::table('properties AS p')
            ->select('p.*',
                DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `scores`'),
                DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `review_count`'),
                DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.tag_name) SEPARATOR ",") FROM property_tags AS pt LEFT JOIN tags as t ON t.tag_id = pt.pt_tag_id WHERE pt.pt_property_id = p.property_id AND pt.pt_inactive = 0) AS `tags`'),
                DB::raw('(SELECT GROUP_CONCAT(CONCAT(pi.property_image_name) SEPARATOR ",") FROM property_images AS pi WHERE pi.property_id = p.property_id) AS `property_image_name`')
            )
            ->where([
                ['property_inactive', '=', '0'],
                ['property_user_id','=',$id]
            ])
            ->get();
            foreach($results as $r) {
                if(is_null($r->scores)) {
                    $r->scores = "No Reviews Yet";
                    $r->review_count = 0;
                } else {
                    $r->review_count = count(explode(',', $r->review_count));
                    $r->scores = array_sum(explode(',', $r->scores))/count(explode(',', $r->scores));
                }
                $r->tags = explode(',', $r->tags);
                $r->property_image_name = explode(',', $r->property_image_name);
            }
            $user = DB::table('users AS u')
                        ->select('u.name', 'u.id', 'u.email',
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(b.booking_id) SEPARATOR ",") FROM bookings AS b LEFT JOIN properties AS p ON p.property_id=b.booking_propertyID WHERE b.booking_userID = u.id AND b.booking_inactive = 0) AS `bookings`'),
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(props.property_id) SEPARATOR ",") FROM properties AS props WHERE props.property_user_id = u.id) AS `properties`')
                        )
                        ->where([
                            ['u.id', $id],
                            ['u.inactive', 0]
                        ])
                        ->first();

            if(isset($user) && !empty($user) && !is_null($user)) {

                $bookings = DB::table('bookings AS b')
                                ->select('b.*', 'p.*')
                                ->where([
                                    ['b.booking_inactive', 0],
                                    ['b.booking_startDate', '>', time()],
                                    ['b.booking_approved', 0],
                                    ['b.booking_denied', 0]
                                ])
                                ->whereIn('b.booking_id', explode(',', $user->bookings))
                                ->leftJoin('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                                ->get();

                foreach($bookings as $b) {
                    $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                    $b->booking_endDate = date('d/m/Y', $b->booking_endDate);
                }


                $abookings = DB::table('bookings AS b')
                                ->select('b.*', 'p.*')
                                ->where([
                                    ['b.booking_inactive', 0],
                                    ['b.booking_startDate', '>', time()],
                                    ['b.booking_approved', 1],
                                    ['b.booking_denied', 0]
                                ])
                                ->whereIn('b.booking_id', explode(',', $user->bookings))
                                ->leftJoin('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                                ->get();

                foreach($abookings as $b) {
                    $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                    $b->booking_endDate = date('d/m/Y', $b->booking_endDate);
                }

                $pbookings = DB::table('bookings AS b')
                                ->select('b.*', 'p.*')
                                ->where([
                                    ['b.booking_inactive', 0],
                                    ['b.booking_endDate', '<', time()],
                                    ['b.booking_approved', 1],
                                    ['b.booking_denied', 0],
                                    ['b..booking_userID', $user->id]
                                ])
                                ->leftJoin('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                                ->get();

                foreach($pbookings as $b) {
                    $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                    $b->booking_endDate = date('d/m/Y', $b->booking_endDate);
                }

                $dbookings = DB::table('bookings AS b')
                                ->select('b.*', 'p.*')
                                ->where([
                                    ['b.booking_inactive', 0],
                                    ['b.booking_approved', 0],
                                    ['b.booking_denied', 1]
                                ])
                                ->whereIn('b.booking_id', explode(',', $user->bookings))
                                ->leftJoin('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                                ->get();

                foreach($dbookings as $b) {
                    $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                    $b->booking_endDate = date('d/m/Y', $b->booking_endDate);
                }

                $listings = DB::table('property_listing AS l')
                                ->join('project.properties AS p', 'l.property_id', '=', 'p.property_id')
                                ->where('p.property_user_id', '=', $id)
                                ->get();

                $reviews = DB::table('tennant_reviews AS t')
                                ->where([
                                    ['trs_tennant_id', $id],
                                    ['trs_inactive', 0]
                                ])
                                ->join('users AS u', 'u.id', '=', 'trs_reviewer_id')
                                ->get();

                $average_guest_score = 0;
                $count = 0;

                foreach($reviews as $r) {
                    $r->trs_submitted_at = date('d/m/Y', $r->trs_submitted_at);
                    $r->trs_edited_at = date('d/m/Y', $r->trs_edited_at);
                    $count = $count + 1;
                    $average_guest_score = $average_guest_score + $r->trs_score;
                }

                if ($count != 0) {
                    $average_guest_score = $average_guest_score/$count;
                } else {
                    $average_guest_score = 'No reviews yet';
                }

                foreach($listings as $l) {
                    $l->start_date = date('d/m/Y', $l->start_date);
                    $l->end_date = date('d/m/Y', $l->end_date);
                }

                $page_owner = false;

                $id = Auth::id();

                if(is_null($id) || !isset($id) || empty($id)) {
                    $page_owner = false;
                } else if($id == $user->id) {
                    $page_owner = true;
                }


                $aa_bookings = DB::table('bookings as b')
                                ->select('b.*', 'u.*',
                                    DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.trs_score) SEPARATOR ",") FROM tennant_reviews AS t WHERE t.trs_inactive = 0 AND t.trs_tennant_id = u.id) AS `scores`')
                                )
                                ->where([
                                    ['p.property_user_id', $id],
                                    ['b.booking_inactive', 0],
                                    ['b.booking_startDate', '>', time()],
                                    ['b.booking_approved', 0],
                                    ['b.booking_denied', 0]
                                ])
                                ->join('users AS u', 'u.id', '=', 'b.booking_userID') // gets user info for each of the people booking
                                ->join('properties AS p', 'p.property_id', 'b.booking_propertyID')
                                ->get();

                foreach($aa_bookings as $b) {
                    $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                    $b->booking_endDate = date('d/m/Y', $b->booking_endDate);

                    $scores = explode(',', $b->scores);
                    $scores = array_sum($scores)/count($scores);
                    $b->scores = $scores;
                }

                $ua_bookings = DB::table('bookings as b')
                                ->select('b.*', 'u.*',
                                    DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.trs_score) SEPARATOR ",") FROM tennant_reviews AS t WHERE t.trs_inactive = 0 AND t.trs_tennant_id = u.id) AS `scores`')
                                )
                                ->where([
                                    ['p.property_user_id', $id],
                                    ['b.booking_inactive', 0],
                                    ['b.booking_startDate', '>', time()],
                                    ['b.booking_approved', 1],
                                    ['b.booking_denied', 0]
                                ])
                                ->join('users AS u', 'u.id', '=', 'b.booking_userID') // gets user info for each of the people booking
                                ->join('properties AS p', 'p.property_id', 'b.booking_propertyID')
                                ->get();

                foreach($ua_bookings as $b) {
                    $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                    $b->booking_endDate = date('d/m/Y', $b->booking_endDate);

                    $scores = explode(',', $b->scores);
                    $scores = array_sum($scores)/count($scores);
                    $b->scores = $scores;
                }

                $pa_bookings = DB::table('bookings as b')
                                ->select('b.*', 'u.*',
                                    DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.trs_score) SEPARATOR ",") FROM tennant_reviews AS t WHERE t.trs_inactive = 0 AND t.trs_tennant_id = u.id) AS `scores`')
                                )
                                ->where([
                                    ['p.property_user_id', $id],
                                    ['b.booking_inactive', 0],
                                    ['b.booking_endDate', '<', time()],
                                    ['b.booking_approved', 1],
                                    ['b.booking_denied', 0]
                                ])
                                ->join('users AS u', 'u.id', '=', 'b.booking_userID') // gets user info for each of the people booking
                                ->join('properties AS p', 'p.property_id', 'b.booking_propertyID')
                                ->get();

                foreach($pa_bookings as $b) {
                    $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                    $b->booking_endDate = date('d/m/Y', $b->booking_endDate);

                    $scores = explode(',', $b->scores);
                    $scores = array_sum($scores)/count($scores);
                    $b->scores = $scores;
                }

                return view('view_user',
                    ['user' => $user,
                    'bookings' => $bookings,
                    'listings' => $listings,
                    'properties' => $results,
                    'page_owner' => $page_owner,
                    'reviews' => $reviews,
                    'guest_score' => $average_guest_score,
                    'abookings' => $abookings,
                    'pbookings' => $pbookings,
                    'aa_bookings' => $aa_bookings,
                    'ua_bookings' => $ua_bookings,
                    'pa_bookings' => $pa_bookings,
                    'dbookings' => $dbookings
                    ]
                );
            }
        }

        return view('user_not_found');
    }
}
