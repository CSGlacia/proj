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
        $this->middleware(['role:admin|super-admin']);

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
            $result['username'] = $username->name;

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
        $tennant_reviews = DB::table('tennant_reviews AS t')
                    ->select('t.*')
                    ->where('t.trs_inactive', 0)
                    ->get();
        $tennants = [];
        foreach($tennant_reviews as $review){
            $tennant = [];
            $reviewer_name = DB::table('users as u')
                            ->select('u.name')
                            ->where('u.id', $review->trs_reviewer_id)
                            ->first();
            $tennant['reviewer_name'] = $reviewer_name->name;

            $tennant_name = DB::table('users as u')
                            ->select('u.name')
                            ->where('u.id', $review->trs_tennant_id)
                            ->first();

            $tennant['property_name'] = $tennant_name->name;

            $tennant['booking_id'] = $review->trs_booking_id;
            $tennant['review_id'] = $review->trs_id;
            array_push($tennants,$tennant);
        }
        $property_reviews = DB::table('property_reviews AS p')
                    ->select('p.*')
                    ->where('p.prs_inactive', 0)
                    ->get();
        $properties = [];
        foreach($property_reviews as $review){
            $property = [];
            $reviewer_name = DB::table('users as u')
                            ->select('u.name')
                            ->where('u.id', $review->prs_reviewer_id)
                            ->first();
            $property['reviewer_name'] = $reviewer_name->name;

            $property_name = DB::table('properties as p')
                            ->select('p.property_title')
                            ->where('p.property_id', $review->prs_property_id)
                            ->first();
            $property['property_name'] = $property_name->property_title;

            $property['booking_id'] = $review->prs_booking_id;
            $property['review_id'] = $review->prs_id;
            array_push($properties,$property);
        }
        return view('view_all_reviews',
        [
            'property_review' => $properties,
            'tennant_review' => $tennants,
        ]);
    }

    public function admin_delete_tennant_review(Request $request){
        $review_id = $request->input('review_id');
        $changed = DB::table('tennant_reviews AS t')
                    ->where([
                        ['t.trs_id', $review_id],
                    ])
                    ->update(['t.trs_inactive' => 1]);

        if(!empty($changed)) {
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'error']);
    }
    public function admin_delete_property_review(Request $request){
        $review_id = $request->input('review_id');
        $changed = DB::table('property_reviews AS p')
                    ->where([
                        ['p.prs_id', $review_id],
                    ])
                    ->update(['p.prs_inactive' => 1]);

        if(!empty($changed)) {
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'error']);
    }
<<<<<<< HEAD
=======
    public function create_advertiser(Request $request){
        if($request->isMethod('GET')){
            $id = Auth::id();
            $users = DB::table('users as u')
                        ->select('id','name')
                        ->where('id','<>',$id)
                        ->get();
            $advertiser = [];
            foreach($users as $user){
                $user_ = User::find($user->id);
                if($user_->hasRole('advertiser')){
                    $advertiser[$user->id] = True;
                }
                else{
                    $advertiser[$user->id] = False;
                }
            }

            return view('admin_advertiser',
            [
                'users' => $users,
                'advertisers' => $advertiser,
            ]);
        }
        else if($request->isMethod('POST')){
            $id = $request->input('user_id');
            $user = User::find($id);
            if($user->hasRole('advertiser')){
                $user->removeRole('advertiser');
                $user->assignRole('user');
            }
            else{
                $user->assignRole('advertiser');
                $user->removeRole('user');
            }
            return json_encode(['status' => 'success']);
        }
    }
    public function creater(Request $request){
        $ad_role = Role::findByName('admin');
        $ad_role->givePermissionTo('can advertise');
        
        return $ad_role;
    }
    public function become_admin(Request $request){
        $user = Auth::user();
        $user->assignRole('admin');
        return $user;
    }
>>>>>>> fedca5c8cfcc6cda8697d065719c81e50eeadc66
}

?>