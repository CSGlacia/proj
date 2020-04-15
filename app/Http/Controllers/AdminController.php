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
        $results = DB::table('bookings AS c')
                    ->select('c.*')
                    ->get();
    }

    public function all_reviews(Request $request){

    }
}
?>