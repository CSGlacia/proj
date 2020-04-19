<?php

namespace App\Http\Controllers;
// Include the SDK using the Composer autoloader
require '../vendor/autoload.php';
use \Aws\S3\S3Client;
use \Aws\S3\Exception\S3Exception;

use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Mail;

class GeneralController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function view_property(Request $request) {
        $results = DB::table('properties AS p')
                        ->select('p.*',
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `scores`'),
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `review_count`'),
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.tag_name) SEPARATOR ",") FROM property_tags AS pt LEFT JOIN tags as t ON t.tag_id = pt.pt_tag_id WHERE pt.pt_property_id = p.property_id AND pt.pt_inactive = 0) AS `tags`'),
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(pi.property_image_name) SEPARATOR ",") FROM property_images AS pi WHERE pi.property_id = p.property_id) AS `property_image_name`')
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
            $r->tags = explode(',', $r->tags);
            $r->property_image_name = explode(',', $r->property_image_name);
        }

        $suburbs = DB::table('properties AS p')
                        ->select('p.property_suburb')
                        ->distinct()
                        ->get();




        $tags = DB::table('tags')
                ->get();


        $tag_ret_arr = [];

        foreach($tags as $r) {
            $tag_ret_arr[] = ['id' => $r->tag_id, 'text' => $r->tag_name];
        }

        return view('view_properties',
                    [
                        'properties' => $results,
                        'tags' => $tag_ret_arr,
                        'suburbs' => $suburbs

                    ]
        );
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

            $tags = DB::table('property_tags AS t')
                    ->where([
                        ['t.pt_inactive', 0],
                        ['t.pt_property_id', $id]
                    ])
                    ->join('tags', 'tag_id', '=', 'pt_tag_id')
                    ->get();


            $tag_ret_arr = [];

            foreach($tags as $r) {
                $tag_ret_arr[] = ['id' => $r->tag_id, 'text' => $r->tag_name];
            }


            return view('property',
                            ['p' => $prop,
                            'bookings' => $bookings,
                            'reviews' => $reviews,
                            'images' => $prop_images,
                            'page_owner' => $page_owner,
                            'cal_bookings' => $cal_booking_arr,
                            'cal_listings' => $cal_listing_arr,
                            'tags' => $tag_ret_arr
                        ]
                );
        }
    }

    public function get_property_tags(Request $request) {
        $term = $request->input('term');

        if(isset($term) && !is_null($term) && !empty($term)) {
            $results = DB::table('tags AS t')
                            ->where('t.tag_name', 'LIKE', '%'.$term.'%')
                            ->get();

            $ret_arr = [];

            foreach($results as $r) {
                $ret_arr[] = ['id' => $r->tag_id, 'text' => $r->tag_name];
            }

            return json_encode($ret_arr);
        }
    }

    public function home()
    {
        return view('home');
    }

    public function property_search(Request $request) {
        $rating = $request->input('rating');
        $name = $request->input('name');
        $address = $request->input('address');
        $suburbs = $request->input('suburbs');
        $tags = $request->input('tags');
        $beds = $request->input('beds');
        $baths = $request->input('baths');
        $cars = $request->input('cars');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $include_unrated = $request->input('include_unrated');
        $props = DB::table('properties AS p')
                    ->select('p.property_id');

        //results that do not meet rating requirement are placed in this array
        $bad_ratings = [];
        if(isset($rating) && !empty($rating) && !is_null($rating) && isset($include_unrated) && !empty($include_unrated) && !is_null($include_unrated)) {
            $include_unrated = ($include_unrated == 'true');

            $rated_props = DB::table('properties AS p')
                            ->select('p.property_id',
                                DB::raw('(SELECT SUM(r.prs_score) FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `ratings`'),
                                DB::raw('(SELECT COUNT(r.prs_score) FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `num_ratings`')
                            )
                            ->get();

            foreach($rated_props as $p) {
                if(isset($p->ratings) && !is_null($p->ratings) && !empty($p->ratings) && $p->num_ratings > 0) {
                    $avg_score = $p->ratings/$p->num_ratings;
                } else {
                    $avg_score = 0;
                }

                if($avg_score == 0 && $include_unrated == false) {
                    $bad_ratings[] = $p->property_id;
                } else if($avg_score > 0 && $avg_score < $rating) {
                    $bad_ratings[] = $p->property_id;
                }
            }
            $bad_ratings = array_unique($bad_ratings);
        }

        if(isset($name) && !empty($name) && !is_null($name)) {
            $props->where('p.property_title', 'LIKE', '%'.$name.'%');
        }

        if(isset($address) && !is_null($address) && !empty($address)) {
            $props->where('p.property_address', 'LIKE', '%'.$address.'%');
        }

        if(isset($suburbs) && !empty($suburbs) && !is_null($suburbs)) {
            $suburbs = explode(',' ,$suburbs);
            $props->whereIn('p.property_suburb', $suburbs);
        }

        if(isset($tags) && !empty($tags) && !is_null($tags)) {
            $tags = explode(',', $tags);

            $props->join('property_tags AS pt', 'pt.pt_property_id', '=', 'p.property_id')
                    ->whereIn('pt.pt_tag_id', $tags)
                    ->groupBy('p.property_id');
        }

        if(isset($beds) && !empty($beds) && !is_null($beds)) {
            $props->where('p.property_beds', '>=', $beds);
        }

        if(isset($baths) && !empty($baths) && !is_null($baths)) {
            $props->where('p.property_baths', '>=', $baths);
        }

        if(isset($cars) && !empty($cars) && !is_null($cars)) {
            $props->where('p.property_cars', '>=', $cars);
        }


        $bad_start_dates = [];
        if(isset($start_date) && !empty($start_date) && !is_null($start_date)) {
            $start_date = explode('/', $start_date);
            $start_date = $start_date[2].'-'.$start_date[1].'-'.$start_date[0];
            $start_date = strtotime($start_date);

            $start_dates = DB::table('properties AS p')
                            ->select('p.property_id', 'p.property_always_list',
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(b.booking_startDate, "," , b.booking_endDate) SEPARATOR "~") FROM bookings AS b WHERE b.booking_inactive = 0 AND p.property_id=b.booking_propertyID AND '.$start_date.' >= b.booking_startDate AND '.$start_date.' <= b.booking_endDate) AS `bookings`'),
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(l.start_date, "," , l.end_date) SEPARATOR "~") FROM property_listing AS l WHERE l.inactive = 0 AND p.property_id=l.property_id AND '.$start_date.' >= l.start_date AND '.$start_date.' <= l.end_date) AS `listings`')
                            )
                            ->get();

            foreach($start_dates AS $s) {
                if(!is_null($s->bookings) || (is_null($s->listings) && $s->property_always_list == 0)) {
                    $bad_start_dates[] = $s->property_id;
                }
            }

            $bad_start_dates = array_unique($bad_start_dates);
        }

        $bad_end_dates = [];
        if(isset($end_date) && !empty($end_date) && !is_null($end_date)) {
            $end_date = explode('/', $end_date);
            $end_date = $end_date[2].'-'.$end_date[1].'-'.$end_date[0];
            $end_date = strtotime($end_date);

            $end_dates = DB::table('properties AS p')
                            ->select('p.property_id', 'p.property_always_list',
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(b.booking_startDate, "," , b.booking_endDate) SEPARATOR "~") FROM bookings AS b WHERE b.booking_inactive = 0 AND p.property_id=b.booking_propertyID AND '.$end_date.' >= b.booking_startDate AND '.$end_date.' <= b.booking_endDate) AS `bookings`'),
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(l.start_date, "," , l.end_date) SEPARATOR "~") FROM property_listing AS l WHERE l.inactive = 0 AND p.property_id=l.property_id AND '.$end_date.' >= l.start_date AND '.$end_date.' <= l.end_date) AS `listings`')
                            )
                            ->get();

            foreach($end_dates AS $e) {
                if(!is_null($e->bookings) || (is_null($e->listings) && $e->property_always_list == 0)) {
                    $bad_end_dates[] = $e->property_id;
                }
            }

            $bad_end_dates = array_unique($bad_end_dates);
        }


        $props = $props->whereNotIn('p.property_id', $bad_ratings)
                        ->whereNotIn('p.property_id', $bad_start_dates)
                        ->whereNotIn('p.property_id', $bad_end_dates)
                        ->get();

        $grab_ids = [];

        foreach($props as $p) {
            $grab_ids[] = $p->property_id;
        }

        $results = DB::table('properties AS p')
                ->select('p.*',
                    DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `scores`'),
                    DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `review_count`'),
                    DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.tag_name) SEPARATOR ",") FROM property_tags AS pt LEFT JOIN tags as t ON t.tag_id = pt.pt_tag_id WHERE pt.pt_property_id = p.property_id AND pt.pt_inactive = 0) AS `tags`')
                )
                ->where([
                    ['property_inactive', '=', '0']
                ])
                ->whereIn('p.property_id', $grab_ids)
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
        }

        $ret_str = '';

        foreach ($results as $r) {
            $ret_str .= '
            <div class="row card item-card cursor-pointer" name="view_property" data-id="'.$r->property_id.'" style="margin:0px; border:none;">
                <div class="col-sm-12 col-md-12 col-lg-12 card-body" >
                    <div class="card-title">
                      <h3>'.$r->property_title.'</h3>
                    </div>
                    <div class="card-text">
                      <div style="margin:5px;">
                        <span><i class="fas fa-bed"></i>&nbsp;'.$r->property_beds.'</span>
                        <span><i class="fas fa-bath"></i>&nbsp;'.$r->property_baths.'</span>
                        <span><i class="fas fa-car"></i>&nbsp;'.$r->property_cars.'</span>
                      </div>
                      <div>'.$r->property_address.'</div>
                      <div style="margin:5px;">'.$r->property_desc.'</div>
                      <div>';

          foreach($r->tags as $t) {
             $ret_str .= '<span class="badge badge-secondary">'.$t.'</span>';
          }

             $ret_str .= '</div>
                      <div><i class="fas fa-star';
                      if($r->scores > 2.5 && $r->scores != 'No Reviews Yet') {
                            $ret_str .= 'gold-star';
                        }

                        $ret_str .= '"></i>&nbsp;'.$r->scores;

                        if($r->scores != "No Reviews Yet") {
                           $ret_str .= $r->review_count.' Review(s))';
                        }
                $ret_str .= '</div>
                    </div>
                </div>
            </div>';
        }

        $status = 'success';

        if($ret_str == '') {
            $status = "no_results";
        }

        return json_encode(['status' => $status, 'data' => $ret_str]);
    }

    /* Email stuff when not logged in*/
    public static function sendRegisterEmail($email)
    {
        Mail::send('emails.register', ['email'=>$email], function ($message) use ($email)
        {
            $message->from('turtleaccommodation@gmail.com', 'TurtleTeam');
            $message->to($email);
        });

    }

    public function map_search(Request $request) {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius');

        if(isset($lat) && !empty($lat) && !is_null($lat) && isset($lng) && !empty($lng) && !is_null($lng) && isset($radius) && !empty($radius) && !is_null($radius)) {
            $radius = $radius/1000;

            $props = DB::table('properties AS p')
                        ->where('p.property_inactive', 0)
                        ->get();

            $neg_props = [];

            foreach($props as $p) {
                $distance = $this->calculate_lat_lng_distance($lat, $lng, $p->property_lat, $p->property_lng);
                if($distance > $radius) {
                    $neg_props[] = $p->property_id;
                }
            }

            $neg_props = array_unique($neg_props);
            $results = DB::table('properties AS p')
                    ->select('p.*',
                        DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `scores`'),
                        DB::raw('(SELECT GROUP_CONCAT(CONCAT(r.prs_score) SEPARATOR ",") FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `review_count`'),
                        DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.tag_name) SEPARATOR ",") FROM property_tags AS pt LEFT JOIN tags as t ON t.tag_id = pt.pt_tag_id WHERE pt.pt_property_id = p.property_id AND pt.pt_inactive = 0) AS `tags`')
                    )
                    ->where([
                        ['property_inactive', '=', '0']
                    ])
                    ->whereNotIn('p.property_id', $neg_props)
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
            }

            $ret_str = '';

            foreach ($results as $r) {
                $ret_str .= '
                <div class="row card item-card cursor-pointer" name="view_property" data-id="'.$r->property_id.'" style="margin:0px; border:none;">
                    <div class="col-sm-12 col-md-12 col-lg-12 card-body" >
                        <div class="card-title">
                          <h3>'.$r->property_title.'</h3>
                        </div>
                        <div class="card-text">
                          <div style="margin:5px;">
                            <span><i class="fas fa-bed"></i>&nbsp;'.$r->property_beds.'</span>
                            <span><i class="fas fa-bath"></i>&nbsp;'.$r->property_baths.'</span>
                            <span><i class="fas fa-car"></i>&nbsp;'.$r->property_cars.'</span>
                          </div>
                          <div>'.$r->property_address.'</div>
                          <div style="margin:5px;">'.$r->property_desc.'</div>
                          <div>';

              foreach($r->tags as $t) {
                 $ret_str .= '<span class="badge badge-secondary">'.$t.'</span>';
              }
                            
                 $ret_str .= '</div>
                          <div><i class="fas fa-star';
                          if($r->scores > 2.5 && $r->scores != 'No Reviews Yet') {
                                $ret_str .= 'gold-star';
                            } 

                            $ret_str .= '"></i>&nbsp;'.$r->scores;

                            if($r->scores != "No Reviews Yet") {
                               $ret_str .= $r->review_count.' Review(s))';
                            }
                    $ret_str .= '</div>
                        </div>
                    </div>
                </div>';
            }

            $status = 'success';

            if($ret_str == '') {
                $status = "no_results";
            }

            return json_encode(['status' => $status, 'data' => $ret_str]);

        }

        return json_encode(['status' => 'error']);
    }

    public function calculate_lat_lng_distance($to_lat, $to_lng, $from_lat, $from_lng) {
        $latFrom = deg2rad($from_lat);
        $lonFrom = deg2rad($from_lng);
        $latTo = deg2rad($to_lat);
        $lonTo = deg2rad($to_lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * 6371;
    }
}
