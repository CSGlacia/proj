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
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(a.animals_type) SEPARATOR ",") FROM property_animals AS pa LEFT JOIN animals as a ON a.animals_id = pa.property_animals_animalID WHERE pa.property_animals_propertyID = p.property_id AND pa.property_animals_inactive = 0) AS `animals`'),
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

        $animals = DB::table('animals')
                ->get();

        $animals_ret_arr = [];

        foreach($animals as $r) {
            $animals_ret_arr[] = ['id' => $r->animals_id, 'text' => $r->animals_type];
        }


        return view('view_properties',
                    [
                        'properties' => $results,
                        'tags' => $tag_ret_arr,
                        'animals' => $animals_ret_arr,
                        'suburbs' => $suburbs

                    ]
        );
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
        $insert_arr = [];
        $user_id = Auth::id();
        if (!is_null($user_id) && isset($user_id)) {
            $insert_arr['vp_user_id'] = $user_id;
        }

        $insert_arr['vp_property_id'] = $id;
        $insert_arr['vp_viewed_at'] = time();
        DB::table('view_property_data')->insert($insert_arr);

        $bucket = 'turtle-database';

        $s3 = new \Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => 'ap-southeast-2'
        ]);

        $prop = DB::table('properties AS p')
                    ->select('p.*',
                        DB::raw('(SELECT SUM(r.prs_score) FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `ratings`'),
                        DB::raw('(SELECT COUNT(r.prs_score) FROM property_reviews AS r WHERE r.prs_inactive = 0 AND r.prs_property_id = p.property_id) AS `num_ratings`')
                    )
                    ->where([
                        ['p.property_id', $id],
                        ['p.property_inactive', 0]
                    ])
                    ->first();

        $prop_view_count = DB::table('view_property_data AS p')
        ->select('p.*'
        )
        ->where([
            ['p.vp_property_id', $id],
        ])
        ->get();
        $view_count = 0;
        foreach($prop_view_count as $p) {
            $view_count = $view_count + 1;
        }

        $prop_data = DB::table('bookings AS b')
        ->select('b.*', 'u.*'
        )
        ->where([
            ['b.booking_propertyID', $id],
        ])
        ->join('users AS u', 'u.id', '=', 'b.booking_userID')
        ->get();

        $total_age = 0;
        $total_persons = 0;
        $row_count = 0;
        foreach($prop_data as $d) {
            // calc avg age
            $total_age += $d->age;

            // average tennant count
            $total_persons += $d->booking_persons;
            $row_count++;
        }

        if ($row_count != 0) {
            $average_age = $total_age/$row_count;
            $average_persons = $total_persons/$row_count;
        } else {
            $average_age = 'No age data available';
            $average_persons = 'No tennant count data available';
        }


        if(isset($prop->ratings) && !is_null($prop->ratings) && !empty($prop->ratings) && $prop->num_ratings > 0) {
            $avg_score = $prop->ratings/$prop->num_ratings;
        } else {
            $avg_score = 0;
        }
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
                                ['b.booking_approved', 0],
                                ['b.booking_denied', 0]
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

            $abookings = DB::table('bookings as b')
                            ->select('b.*', 'u.*',
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.trs_score) SEPARATOR ",") FROM tennant_reviews AS t WHERE t.trs_inactive = 0 AND t.trs_tennant_id = u.id) AS `scores`')
                            )
                            ->where([
                                ['b.booking_propertyID', $id],
                                ['b.booking_inactive', 0],
                                ['b.booking_startDate', '>', time()],
                                ['b.booking_approved', 1]
                            ])
                            ->join('users AS u', 'u.id', '=', 'b.booking_userID') // gets user info for each of the people booking
                            ->get();

            foreach($abookings as $b) {
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
                                ['b.booking_propertyID', $id],
                                ['b.booking_inactive', 0],
                                ['b.booking_endDate', '<', time()],
                                ['b.booking_approved', 1],
                                ['b.booking_denied', 0]
                            ])
                            ->join('users AS u', 'u.id', '=', 'b.booking_userID') // gets user info for each of the people booking
                            ->get();

            foreach($pa_bookings as $b) {
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
                                    ['b.booking_approved', 1]
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
                            'tags' => $tag_ret_arr,
                            'abookings' => $abookings,
                            'avg_score' => $avg_score,
                            'pa_bookings' => $pa_bookings,
                            'page_count' => $view_count,
                            'avg_age' => $average_age,
                            'avg_persons' => $average_persons,
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
        $animals = $request->input('animals');
        $props = DB::table('properties AS p')
                    ->select('p.property_id');


        /*
        TODO: We want to record the search terms and insert into table: search_data
            Hint: set $insert_arr = [];
            Then to add a column e.g. search_name do: $insert_arr['search_name'] = $name
            Also for search_data_searched_at use time();
            IMPORTANT: For tags and suburbs we want to insert as a csv string so insert into $insert_arr before we do the explode();
            i.e. $insert_arr['search_tags'] => $tags;
                $tags = explode(',', $tags);
            You can check the data types for the column using sql workbench
            1. check if user id exists using $user_id = Auth::id(); and validate that  $user_id exists, isnt null and is numeric (if it doesnt exists dont insert the user_id into table (it will just show as null))
            2. For each of the above search terms you will see where we validate if they exist. In each if() statement below, youll need to add the search term to our insert array (these terms are guarenteed to exist inside the if statements)
            3. After all of the if (~line 677) statements do DB::table('search_data')->insert($insert_arr);

        */
        $insert_arr = [];
        $insert_arr['search_data_searched_at'] = time();

        //results that do not meet rating requirement are placed in this array
        $bad_ratings = [];
        if(isset($rating) && !empty($rating) && !is_null($rating) && isset($include_unrated) && !empty($include_unrated) && !is_null($include_unrated)) {
            $include_unrated = ($include_unrated == 'true');
            $insert_arr['search_min_rating'] = $rating;
            $insert_arr['search_unrated'] = $include_unrated;

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



        // check if there is a user logged in
        $user_id = Auth::id();

        if (!is_null($user_id) && isset($user_id)) {
            $insert_arr['search_data_user_id'] = $user_id;
        }

        if(isset($name) && !empty($name) && !is_null($name)) {
            $props->where('p.property_title', 'LIKE', '%'.$name.'%');
            $insert_arr['search_name'] = $name;
        }

        if(isset($address) && !is_null($address) && !empty($address)) {
            $props->where('p.property_address', 'LIKE', '%'.$address.'%');
            $insert_arr['search_address'] = $address;
        }

        if(isset($suburbs) && !empty($suburbs) && !is_null($suburbs)) {
            $suburbs = explode(',' ,$suburbs);
            $props->whereIn('p.property_suburb', $suburbs);

            $suburbs = implode('~', $suburbs);
            $insert_arr['search_suburb'] = $suburbs;

            
        }

        if(isset($tags) && !empty($tags) && !is_null($tags)) {
            $tags = explode(',', $tags);

            $props->join('property_tags AS pt', 'pt.pt_property_id', '=', 'p.property_id')
                    ->whereIn('pt.pt_tag_id', $tags)
                    ->groupBy('p.property_id');

            $selected_tags = DB::table('tags')
                                ->whereIn('tag_id', $tags)
                                ->get();

            $tag_arr = [];

            foreach($selected_tags as $t) {
                $tag_arr[] = $t->tag_name;
            }

            $tag_str = implode('~', $tag_arr);

            $insert_arr['search_tags'] = $tag_str;
        }

        // TODO: Add to analytics that this particular user has these particular pet(s).
        // Animals.

        if(isset($animals) && !empty($animals) && !is_null($animals)) {
            $insert_arr['search_animals'] = $animals;
            $animals = explode(',', $animals);

            $props->join('property_animals AS pa', 'pa.property_animals_propertyID', '=', 'p.property_id')
                    ->whereIn('pa.property_animals_animalID', $animals)
                    ->groupBy('p.property_id');
        }


        if(isset($beds) && !empty($beds) && !is_null($beds)) {
            $props->where('p.property_beds', '>=', $beds);
            $insert_arr['search_beds'] = $beds;
        }

        if(isset($baths) && !empty($baths) && !is_null($baths)) {
            $props->where('p.property_baths', '>=', $baths);
            $insert_arr['search_baths'] = $baths;
        }

        if(isset($cars) && !empty($cars) && !is_null($cars)) {
            $props->where('p.property_cars', '>=', $cars);
            $insert_arr['search_cars'] = $cars;
        }


        $bad_start_dates = [];
        if(isset($start_date) && !empty($start_date) && !is_null($start_date)) {
            $start_date = explode('/', $start_date);
            $start_date = $start_date[2].'-'.$start_date[1].'-'.$start_date[0];
            $start_date = strtotime($start_date);

            $start_dates = DB::table('properties AS p')
                            ->select('p.property_id',
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(b.booking_startDate, "," , b.booking_endDate) SEPARATOR "~") FROM bookings AS b WHERE b.booking_inactive = 0 AND p.property_id=b.booking_propertyID AND '.$start_date.' >= b.booking_startDate AND '.$start_date.' <= b.booking_endDate) AS `bookings`'),
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(l.start_date, "," , l.end_date) SEPARATOR "~") FROM property_listing AS l WHERE l.inactive = 0 AND p.property_id=l.property_id AND '.$start_date.' >= l.start_date AND '.$start_date.' <= l.end_date) AS `listings`')
                            )
                            ->get();

            foreach($start_dates AS $s) {
                if(!is_null($s->bookings) || (is_null($s->listings))) {
                    $bad_start_dates[] = $s->property_id;
                }
            }

            $bad_start_dates = array_unique($bad_start_dates);
            $insert_arr['search_start_date'] = $start_date;
        }

        $bad_end_dates = [];
        if(isset($end_date) && !empty($end_date) && !is_null($end_date)) {
            $end_date = explode('/', $end_date);
            $end_date = $end_date[2].'-'.$end_date[1].'-'.$end_date[0];
            $end_date = strtotime($end_date);

            $end_dates = DB::table('properties AS p')
                            ->select('p.property_id',
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(b.booking_startDate, "," , b.booking_endDate) SEPARATOR "~") FROM bookings AS b WHERE b.booking_inactive = 0 AND p.property_id=b.booking_propertyID AND '.$end_date.' >= b.booking_startDate AND '.$end_date.' <= b.booking_endDate) AS `bookings`'),
                                DB::raw('(SELECT GROUP_CONCAT(CONCAT(l.start_date, "," , l.end_date) SEPARATOR "~") FROM property_listing AS l WHERE l.inactive = 0 AND p.property_id=l.property_id AND '.$end_date.' >= l.start_date AND '.$end_date.' <= l.end_date) AS `listings`')
                            )
                            ->get();

            foreach($end_dates AS $e) {
                if(!is_null($e->bookings) || (is_null($e->listings))) {
                    $bad_end_dates[] = $e->property_id;
                }
            }

            $bad_end_dates = array_unique($bad_end_dates);
            $insert_arr['search_end_date'] = $end_date;
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
                    DB::raw('(SELECT GROUP_CONCAT(CONCAT(t.tag_name) SEPARATOR ",") FROM property_tags AS pt LEFT JOIN tags as t ON t.tag_id = pt.pt_tag_id WHERE pt.pt_property_id = p.property_id AND pt.pt_inactive = 0) AS `tags`'),
                    DB::raw('(SELECT GROUP_CONCAT(CONCAT(a.animals_type) SEPARATOR ",") FROM property_animals AS pa LEFT JOIN animals as a ON a.animals_id = pa.property_animals_animalID WHERE pa.property_animals_propertyID = p.property_id AND pa.property_animals_inactive = 0) AS `animals`')
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
            <div class="row card item-card cursor-pointer" name="view_property" data-id="'.$r->property_id.'" style="margin:0px; border:none; width:50vw;">
                <div class="col-sm-12 col-md-12 col-lg-12 card-body" >';
            $prop_images = DB::table('property_images AS p')
                            ->select('p.property_image_name')
                            ->where([['p.property_id',$r->property_id]])
                            ->first();
            if(isset($prop_images)){
                $ret_str .= '<img class="float-right" height="160vh" src="https://turtle-database.s3-ap-southeast-2.amazonaws.com/'.$prop_images->property_image_name.'">';
            }
            $ret_str .= '<div class="card-title">
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
                      <div><i class="fas fa-star ';
                      if($r->scores > 2.5 && $r->scores != 'No Reviews Yet') {
                            $ret_str .= 'gold-star';
                        }

                        $ret_str .= '"></i>&nbsp;'.$r->scores."(";

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

        DB::table('search_data')->insert($insert_arr);

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
            //you can get $user_id by doing Auth::id() but you need to check if it is not null and numeric because we are in general controller
            //TODO: insert into table: search_map_data ['search_map_data_user_id' => $user_id (if it exists otherwise dont insert this column), 'search_map_data_searched_at' => time(), 'search_lat' => $lat, 'search_lng' => $lng, 'search_radius' => $radius]
            $insert_arr = [];
            $user_id = Auth::id();
            if (!is_null($user_id) && isset($user_id)) {
                $insert_arr['search_map_data_user_id'] = $user_id;
            }

            $insert_arr['search_map_data_searched_at'] = time();
            $insert_arr['search_lat'] = $lat;
            $insert_arr['search_lng'] = $lng;
            $insert_arr['search_radius'] = $radius;
            DB::table('search_map_data')->insert($insert_arr);

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
                <div class="row card item-card cursor-pointer" name="view_property" data-id="'.$r->property_id.'" style="margin:0px; border:none; width:50vw;">
                    <div class="col-sm-12 col-md-12 col-lg-12 card-body" >';
                $prop_images = DB::table('property_images AS p')
                                ->select('p.property_image_name')
                                ->where([['p.property_id',$r->property_id]])
                                ->first();
                if(isset($prop_images)){
                    $ret_str .= '<img class="float-right" height="160vh" src="https://turtle-database.s3-ap-southeast-2.amazonaws.com/'.$prop_images->property_image_name.'">';
                }
                $ret_str .= '<div class="card-title">
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
