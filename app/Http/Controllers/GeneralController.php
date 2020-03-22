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
                        ->select('u.name', 'u.email',
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(b.id, ",", b.propertyID, ",", p.property_address) SEPARATOR "~") FROM bookings AS b LEFT JOIN properties AS p ON p.property_id=b.propertyID WHERE b.userID = u.id) AS `bookings`'),
                            DB::raw('(SELECT GROUP_CONCAT(CONCAT(props.property_address, ",", props.property_desc) SEPARATOR "~") FROM properties AS props WHERE props.property_user_id = u.id) AS `properties`')
                        )
                        ->where([
                            ['u.id', $id],
                            ['u.inactive', 0]
                        ])
                        ->first();
            
            if(isset($user) && !empty($user) && !is_null($user)) {
                $bookings = explode("~", $user->bookings);
                $properties = explode("~", $user->properties);
                
                return view('view_user', 
                    ['user' => $user,
                    'bookings' => $bookings,
                    'properties' => $properties]
                );
            }
        }

        return view('user_not_found');
    }
    public function get_user_id(Request $request) {
        $id = Auth::id();

        if(isset($id) && !empty($id) && !is_null($id)) {
            return json_encode(['status' => 'success', 'id' => $id]);
        }
        return json_encode(['status' => 'error']);
    }
}
