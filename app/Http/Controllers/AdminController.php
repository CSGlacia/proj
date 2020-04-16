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
                    ->where([['b.booking_inactive', 0],
                            ['b.booking_endDate', '>', time()])
                    ->get();
        $results = [];
        foreach($bookings as $booking){
            $result = [];
            $username = DB::table('users as u')
                            ->select('u.name')
                            ->where('u.id', $booking->booking_userID)
                            ->get();
            array_push($result,$username->name);

            $title = DB::table('properties as p')
                            ->select('p.title')
                            ->where('p.property_id', $booking->booking_propertyID)
                            ->get();
            array_push($result,$title->title);
            array_push($result,$booking->booking_id);
            array_push($result,$booking->booking_startDate);
            array_push($result,$booking->booking_endDate);

            array_push($results,$result);
        }
        return view('view_all_bookings',
        [
            'results' => $results,
        ]);
    }

    public function all_reviews(Request $request){

    }
}
?>