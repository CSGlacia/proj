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
                                ->where('b.booking_inactive', 0)
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
                    'properties' => $properties,
                    'page_owner' => $page_owner
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
        $prop = DB::table('properties AS p')
                    ->select('p.*')
                    ->where([
                        ['p.property_id', $id],
                        ['p.property_inactive', 0]
                    ])
                    ->first();

        if(isset($prop) && !empty($prop) && !is_null($prop)) {

            $avail = DB::table('bookings AS b')
                        ->select('b.*')
                        ->where([
                            ['b.booking_propertyID', $id],
                            ['b.booking_inactive', 0]
                        ])
                        ->get();

            foreach($avail as $a) {
                $a->booking_startDate = date('d/m/Y', $a->booking_startDate);
                $a->booking_endDate = date('d/m/Y', $a->booking_endDate);
            }

            return view('property',
                            ['p' => $prop,
                            'avail' => $avail]
                );
        }
    }
}
