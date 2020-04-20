<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Response;

class AdvertiserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        //need to only allow advertisers, admins and super admins
    }

    public function exports(Request $request) {
    	return view('exports');
    }

    public function download_search_data(Request $request) {
    	$data = DB::table('search_data AS s')
    				->leftJoin('users AS u', 'u.id', '=', 's.search_data_user_id')
    				->get();

    	$data_arr = [];

    	$data_arr[] = ['age', 'gender', 'searched_at', 'search_name', 'search_address', 'search_suburb', 'search_tags', 'search_minimum_rating', 'search_unrated', 'search_beds', 'search_baths', 'search_cars', 'search_start_date', 'search_end_date'];


    	$tags = DB::table('tags')
    				->get();

    	$tags = $tags->toArray();

    	$tag_arr = [];

    	foreach($tags as $key => $value) {
    		$tag_arr[$key] = $value->tag_name;
    	}

		foreach($data as $d) {
			$insert_tag_arr = [];

			$tag_split = explode(',', $d->search_tags);

			foreach($tag_split as $key => $value) {
				$insert_tag_arr[] = $tag_arr[$key];
			}

			$tag_str = implode('~', $insert_tag_arr);

			$d->search_suburb = str_replace(',', '~', $d->search_suburb);

			$data_arr[] = [$d->age, $d->gender, $d->search_data_searched_at, $d->search_name, $d->search_address, str_replace(',', '~', $d->search_suburb), $tag_str, $d->search_min_rating, $d->search_unrated, $d->search_beds, $d->search_baths, $d->search_cars, $d->search_start_date, $d->search_end_date];
		}

		$headers = [
			'Content-Type' => 'text/csv'
		];

		$callback = function() use ($data_arr) {
			$FH = fopen('php://output', 'w');
			foreach($data_arr as $row) {
				fputcsv($FH, $row);
			}
			fclose($FH);
		};

		return response()->stream($callback, 200, $headers);
    }

    public function download_georgraphical_search_data(Request $request) {
    	$data = DB::table('search_map_data AS s')
    				->leftJoin('users AS u', 'u.id', '=', 's.search_map_data_user_id')
    				->get();

		$data_arr = [];

    	$data_arr[] = ['age', 'gender', 'searched_at', 'search_lattitude', 'search_longitude', 'search_radius'];

    	foreach($data as $d) {
    		$data_arr[] = [$d->age, $d->gender, $d->search_map_data_searched_at, $d->search_lat, $d->search_lng, $d->search_radius];
    	}

		$headers = [
			'Content-Type' => 'text/csv'
		];

		$callback = function() use ($data_arr) {
			$FH = fopen('php://output', 'w');
			foreach($data_arr as $row) {
				fputcsv($FH, $row);
			}
			fclose($FH);
		};

		return response()->stream($callback, 200, $headers);
    }

    public function download_booking_data(Request $request) {
    	$data = DB::table('bookings AS b')
    				->where('b.booking_inactive', 0)
    				->join('properties AS p', 'p.property_id', '=', 'b.booking_propertyID')
    				->join('users AS u', 'u.id', '=', 'b.booking_userID')
    				->get();


//age (int), gender (M,F,X), booking_start_date (epoch timestamp), booking_end_date (epoch timestamp), booking_suburb (string), booking_tags (~ separated string of property tags), booking_beds (int), booking_baths (int), booking_cars (int), booking_keyword (string of the property's advertised title)

		$data_arr = [];

    	$data_arr[] = ['age', 'gender', 'booking_start_date', 'booking_end_date', 'booking_suburb', 'booking_persons', 'booking_tags', 'booking_beds', 'booking_baths', 'booking_cars', 'booking_keyword'];

    	foreach($data as $d) {

    		$tags = DB::table('tags AS t')
    					->select('t.tag_name')
    					->where([
    						['pt.pt_property_id', $d->property_id],
    						['pt.pt_inactive', 0]
    					])
    					->join('property_tags AS pt', 'pt.pt_tag_id', '=', 't.tag_id')
    					->get();
    		
    		$tag_arr = [];

    		foreach($tags as $t) {
    			$tag_arr[] = $t->tag_name;
    		}

    		$tag_str = implode('~', $tag_arr);

    		$data_arr[] = [$d->age, $d->gender, $d->booking_startDate, $d->booking_endDate, $d->property_suburb, $d->booking_persons, $tag_str, $d->property_beds, $d->property_baths, $d->property_cars, $d->property_title];
    	}

		$headers = [
			'Content-Type' => 'text/csv'
		];

		$callback = function() use ($data_arr) {
			$FH = fopen('php://output', 'w');
			foreach($data_arr as $row) {
				fputcsv($FH, $row);
			}
			fclose($FH);
		};

		return response()->stream($callback, 200, $headers);
    }

    public function download_personal_data(Request $request) {
    	$data = DB::table('users AS u')
    				->get();

		$data_arr = [];

		$data_arr[] = ['age', 'gender', 'name', 'phone', 'email'];

		foreach($data as $d) {
			$data_arr[] = [$d->age, $d->gender, $d->name, $d->phone, $d->email];
		}

		$headers = [
			'Content-Type' => 'text/csv'
		];

		$callback = function() use ($data_arr) {
			$FH = fopen('php://output', 'w');
			foreach($data_arr as $row) {
				fputcsv($FH, $row);
			}
			fclose($FH);
		};

		return response()->stream($callback, 200, $headers);
    }

    public function download_viewing_data(Request $request) {
    	$data = DB::table('view_property_data AS v')
    				->leftJoin('users AS u', 'u.id', '=', 'v.vp_user_id')
    				->join('properties AS p', 'p.property_id', '=', 'v.vp_property_id')
    				->get();
    	
		$data_arr = [];

		$data_arr[] = ['age', 'gender', 'viewed_at', 'property_suburb', 'property_tags', 'property_title', 'property_beds', 'property_baths', 'property_cars'];

		foreach($data as $d) {
			$tags = DB::table('tags AS t')
    					->select('t.tag_name')
    					->where([
    						['pt.pt_property_id', $d->property_id],
    						['pt.pt_inactive', 0]
    					])
    					->join('property_tags AS pt', 'pt.pt_tag_id', '=', 't.tag_id')
    					->get();
    		
    		$tag_arr = [];

    		foreach($tags as $t) {
    			$tag_arr[] = $t->tag_name;
    		}

    		$tag_str = implode('~', $tag_arr);

    		$data_arr[] = [$d->age, $d->gender, $d->vp_viewed_at, $d->property_suburb, $tag_str, $d->property_title, $d->property_beds, $d->property_baths, $d->property_cars];
		}

		$headers = [
			'Content-Type' => 'text/csv'
		];

		$callback = function() use ($data_arr) {
			$FH = fopen('php://output', 'w');
			foreach($data_arr as $row) {
				fputcsv($FH, $row);
			}
			fclose($FH);
		};

		return response()->stream($callback, 200, $headers);
    }
}
