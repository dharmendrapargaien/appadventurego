<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models;

class UserController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * get user porint and start
     * @param  Models\User $user
     * @return Json
     */
    public function getStarPoint(Models\User $user)
    {
        
        $points = Models\UserPoint::select('total_points', 'total_stars')->whereStatus(1)->whereUserId($user->id)->first();
        
        if (empty($points)) {

            return response()->json([
                'status'  => 'fail',
                'message' => "You do not have points."
            ], 500);
        }
        
        return response()->json([
            'status' => 'success',
            'data'   => $points
        ], 200);
    }
}
