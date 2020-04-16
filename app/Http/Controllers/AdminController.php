<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Auth;
use DB;
use App;

class AdminController extends Controller{
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function all_bookings(Request $request){
        $bookings = DB::table('bookings AS c')
                    ->select('c.*')
                    ->where([['c.booking_inactive', 0],
                            ['c.booking_endDate', '>', time()]])
                    ->get();
        $results = [];
        foreach($bookings as $booking){
            $result = [];
            $username = DB::table('users as u')
                            ->select('u.name')
                            ->where('u.id', $booking->booking_userID)
                            ->first();
            array_push($result,$username->name);

            $title = DB::table('properties as p')
                            ->select('p.property_title')
                            ->where('p.property_id', $booking->booking_propertyID)
                            ->first();
            $result['property_title'] = $title->property_title;
            $result['booking_id'] = $booking->booking_id;
            $result['booking_startDate'] = date('d/m/Y', $booking->booking_startDate);
            $result['booking_endDate'] = date('d/m/Y', $booking->booking_endDate);

            array_push($results,$result);
        }
        return view('view_all_bookings',
        [
            'results' => $results
        ]);
    }

    public function admin_delete_bookings(Request $request){
        $booking_id = $request->input('booking_id');
        $changed = DB::table('bookings AS b')
                    ->where([
                        ['b.booking_id', $booking_id],
                        ['b.booking_inactive', 0],

                    ])
                    ->update(['b.booking_inactive' => 1]);

        if(!empty($changed)) {
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'error']);
    }

    public function all_reviews(Request $request){

    }
}
?>