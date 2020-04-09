<?php

namespace App\Http\Controllers;
// Include the SDK using the Composer autoloader
require '../vendor/autoload.php';
use \Aws\S3\S3Client;
use \Aws\S3\Exception\S3Exception;

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
                            ->select('p.*',
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `scores`'),
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `review_count`')
                            )
                            ->where([
                                ['property_inactive', '=', '0']
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
            }
            return view('view_properties',
                        ['properties' => $results]
            );
        } else{

            $searchCritera = [];

            if($address_checkbox == 1){
                array_push($searchCritera, ['p.property_address', 'LIKE', '%'.$query.'%', 'OR']);

            }
            $results = DB::table('properties AS p')
                            ->select('p.*',
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `scores`')
                            )
                            ->where([
                                ['property_inactive', '=', '0'],
                                [$searchCritera]
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
            }
            return view('view_properties',
                        ['properties' => $results]
                        );
        }
    }

    public function view_user(Request $request, $id) {
        if(isset($id) && !is_null($id) && !empty($id) && is_numeric($id)) {
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
                                    ['b.booking_startDate', '>', time()]
                                ])
                                ->whereIn('b.booking_id', explode(',', $user->bookings))
                                ->leftJoin('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
                                ->get();

                foreach($bookings as $b) {
                    $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                    $b->booking_endDate = date('d/m/Y', $b->booking_endDate);
                }

                $properties = DB::table('properties AS p')
                                ->where('p.property_inactive', 0)
                                ->whereIn('p.property_id', explode(',', $user->properties))
                                ->get();

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

                return view('view_user',
                    ['user' => $user,
                    'bookings' => $bookings,
                    'listings' => $listings,
                    'properties' => $properties,
                    'page_owner' => $page_owner,
                    'reviews' => $reviews,
                    'guest_score' => $average_guest_score
                    ]
                );
            }
        }

        return view('user_not_found');
    }

    public function get_user_id(Request $request) {
        $id = Auth::id();
        if(is_null($id)){
            return json_encode(['status' => 'not_logged_in']);
        }
        if(isset($id) && !empty($id)) {
            return json_encode(['status' => 'success', 'id' => $id]);
        }
        return json_encode(['status' => 'error']);
    }

    public function view_one_property(Request $request, $id) {
        $bucket = 'turtle-database';

        $s3 = new \Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => 'ap-southeast-2'
        ]);

        $prop = DB::table('properties AS p')
                    ->select('p.*')
                    ->where([
                        ['p.property_id', $id],
                        ['p.property_inactive', 0]
                    ])
                    ->first();

        $prop_images = DB::table('property_images AS p')
                        ->select('p.property_image_name')
                        ->where([['p.property_id',$id]])
                        ->get();

        /*foreach ($prop_images as $path) {
            try{
                $extension = preg_match('/\./',$path->property_image_name) ? preg_replace('/^.*\./','',$path->property_image_name): '';
                $image = $s3->getObject(array(
                    'Bucket' => $bucket,
                    'Key'    => $path->property_image_name,
                    'ResponseContentType' => 'image/'.$extension,
                ));
                array_push($image_array,$image);
            }
            catch (S3Exception $e) {
                return json_encode(['status' => 'image_fail']);
            }

        }*/


        if(isset($prop) && !empty($prop) && !is_null($prop)) {

            $bookings = DB::table('bookings as b')
                            ->select('b.*', 'u.*',
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.trs_score) SEPARATOR ",") FROM tennant_reviews AS t WHERE t.trs_inactive = 0 AND t.trs_tennant_id = u.id) AS `scores`')
                            )
                            ->where([
                                ['b.booking_propertyID', $id],
                                ['b.booking_inactive', 0],
                                ['b.booking_startDate', '>', time()],
                            ])
                            ->join('users AS u', 'u.id', '=', 'b.booking_userID') // gets user info for each of the people booking
                            ->get();

            foreach($bookings as $b) {
                $b->booking_startDate = date('d/m/Y', $b->booking_startDate);
                $b->booking_endDate = date('d/m/Y', $b->booking_endDate);

                $scores = explode(',', $b->scores);
                $scores = array_sum($scores)/count($scores);
                $b->scores = $scores;
            }

            $reviews = DB::table('property_reviews AS p')
                        ->where([
                            ['prs_property_id', $id],
                            ['prs_inactive', 0]
                        ])
                        ->join('users AS u', 'u.id', '=', 'prs_reviewer_id')
                        ->get();

            foreach($reviews as $r) {
                $r->prs_submitted_at = date('d/m/Y', $r->prs_submitted_at);
                $r->prs_edited_at = date('d/m/Y', $r->prs_edited_at);
            }

            $page_owner = false;

            $user_id = Auth::id();

            if(is_null($user_id) || !isset($user_id) || empty($user_id)) {
                $page_owner = false;
            } else if($user_id == $prop->property_user_id) {
                $page_owner = true;
            }


            //grab listing dates and booking dates for the calendar
            $cal_bookings = DB::table('bookings AS b')
                                ->select('b.booking_startDate', 'b.booking_endDate')
                                ->where([
                                    ['b.booking_propertyID', $id],
                                    ['b.booking_inactive', 0],
                                    ['b.booking_startDate', '>', time()],
                                ])
                                ->get();

            $cal_booking_arr = [];

            foreach($cal_bookings as $c) {
                $cal_booking_arr[] = ['start' => $c->booking_startDate, 'end' => $c->booking_endDate];
            }

            $cal_listings = DB::table('property_listing AS p')
                                ->select('p.start_date', 'p.end_date', 'p.reccurring')
                                ->where([
                                    ['p.inactive', 0],
                                    ['p.property_id', $id]
                                ])
                                ->get();

            $cal_listing_arr = [];

            foreach($cal_listings as $c) {
                $cal_listing_arr[] = ['start' => $c->start_date, 'end' => $c->end_date, 'reccurring' => $c->reccurring];
            }

            return view('property',
                            ['p' => $prop,
                            'bookings' => $bookings,
                            'reviews' => $reviews,
                            'images' => $prop_images,
                            'page_owner' => $page_owner,
                            'cal_bookings' => $cal_booking_arr,
                            'cal_listings' => $cal_listing_arr
                        ]
                );
        }
    }

    public function guest_home()
    {
        return view('guest_home');
    }
}
