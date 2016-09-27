<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\Api\V1\CreateMarkerRequest;
use App\Http\Requests\Api\V1\UpdateMarkerRequest;
use App\Http\Requests\Api\V1\NearestMarkerRequest;
use App\Http\Requests\Api\V1\MarkerVisitRequest;
use App\Http\Requests\Api\V1\EventMarkerFlagRequest;

use App\Models;

class MarkerController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display all markers created by requested user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Models\User $user)
    {
  
        $markers = Models\Marker::select('*',\DB::raw('(SELECT COUNT(marker_visits.id) FROM `marker_visits` WHERE marker_visits.marker_id = markers.id) as marker_visit_count'))->whereUserId($user->id)->whereStatus(1)->orderBy('created_at', 'desc')->get();

        if ($markers->count() == 0) {

            return response()->json([
                'status'  => 'fail',
                'message' => 'Data not found'
            ], 500);
        }
        
        return response()->json([
            'status'             => 'success',
            'data'               => $markers,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateMarkerRequest $request, Models\User $user)
    {

        \DB::beginTransaction();

        //create new user
         $marker_data = [
            'user_id'        => $user->id,
            'marker_type_id' => Models\MarkerType::select('id')->whereTypeSlug($request->marker_type)->first()['id'],
            'name'           => $request->name,
            'description'    => $request->description,
            'lat'            => $request->lat,
            'lon'            => $request->lon,
            'marker_points'  => $request->marker_points,
            'marker_stars'   => $request->marker_stars,
        ];

        //if marker type is event then we need data and time
        if($request->has('marker_type') && $request->get('marker_type') == 'event') {
            
            $marker_data['marker_date']    = $request->marker_date;
            $marker_data['marker_time']    = $request->marker_time;    

        }
        
        if($request->has('marker_type') && $request->get('marker_type') == 'treasure-chest'){

            $userPoints = Models\UserPoint::select('total_points')->whereUserId($user->id)->first()->total_points;
            //check user points. If user don't have enough points then he can not create marker
            if($userPoints < $marker_data["marker_points"]) {

                return response()->json([
                    'status'  => 'fail',
                    'message' => 'You have not enough points for treasure chest marker.'
                ], 500);
            }
            
            Models\UserPoint::whereUserId($user->id)->decrement('total_points', $marker_data["marker_points"]);
        }
        
        $marker = Models\Marker::create($marker_data);

        \DB::commit();
        
        return response()->json([
            'status' => 'success',
            'data'   => $marker
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMarkerRequest $request, Models\User $user, $id)
    {
        
        \DB::beginTransaction();

        $marker = Models\Marker::whereUserId($user->id)->findOrFail($id);
        //create new user
         
        $marker->user_id        = $user->id;
        $marker->marker_type_id = Models\MarkerType::select('id')->whereTypeSlug($request->marker_type)->first()['id'];
        $marker->name           = $request->name;
        $marker->description    = $request->description;
        $marker->lat            = $request->lat;
        $marker->lon            = $request->lon;
        $marker->marker_stars   = $request->marker_stars;
        

        //if marker type is event then we need data and time
        if($request->has('marker_type') && $request->get('marker_type') == 'event') {
            
            $marker->marker_date   = $request->marker_date;
            $marker->marker_time   = $request->marker_time;    

        }
        
        if($request->has('marker_type') && $request->get('marker_type') == 'treasure-chest'){

            $userPoints = Models\UserPoint::select('total_points')->whereUserId($user->id)->first()->total_points;
            //check user points. If user don't have enough points then he can not create marker
            $marker_points = ($request->marker_points - $marker->marker_points);

            if($userPoints < $marker_points) {

                return response()->json([
                    'status'  => 'fail',
                    'message' => 'You have not enough points for treasure chest marker.'
                ], 500);
            }
            
            //balace the marker points in user poiints table
            if($marker_points < 0) {

                Models\UserPoint::whereUserId($user->id)->increment('total_points', abs($marker_points));
            } elseif($marker_points > 0) {

                Models\UserPoint::whereUserId($user->id)->decrement('total_points', $marker_points);
            }
        }

        $marker->marker_points  = $request->marker_points;

        $marker->save();

        \DB::commit();
        
        return response()->json([
            'status' => 'success',
            'data'   => $marker
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Models\User $user, $id)
    {
        
        \DB::beginTransaction();

        $marker = Models\Marker::whereUserId($user->id)->findOrFail($id);
         
        $markerType = Models\MarkerType::select('type_slug')->whereTypeSlug($marker->marker_type_id)->first()['type_slug'];
        
        if($markerType == 'treasure-chest')
            Models\UserPoint::whereUserId($user->id)->increment('total_points', $marker->marker_points);
           
        $marker->delete();

        \DB::commit();
        
        return response()->json([
            'status' => 'success'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markerTypes()
    {

        $marker_types = Models\MarkerType::select('id','type_slug', 'name', 'description','marker_points', 'marker_stars')->where('marker_for' , '!=' , 0)->whereStatus(1)->orderBy('name')->get();
        
        if ($marker_types->count() == 0) {

            return response()->json([
                'status'  => 'fail',
                'message' => 'Data not found'
            ], 500);
        }
        
        return response()->json([
            'status' => 'success',
            'data'   => $marker_types
        ], 200);
    }

    /**
     * list of all marker that comes within range
     * @param  NearestMarkerRequest $request
     * @return Json
     */
    public function getNearestMarker(NearestMarkerRequest $request, Models\User $user)
    {

        $nearest_markers = Models\Marker::select('id','marker_type_id', 'name', 'description', 'marker_points', 'marker_stars', 'marker_date', 'marker_time', 'status', 'created_at', 'updated_at', 'lat', 'lon', \DB::raw("( 3959 * acos ( cos ( radians($request->lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($request->lon) ) + sin ( radians($request->lat) ) * sin( radians( lat ) ) ) ) AS distance"))->having('distance','<',(0.621371 * env('MARKER_RANGE',10000)))->where('user_id', '<>', $user->id)->get();
        
        if ($nearest_markers->count() == 0) {

            return response()->json([
                'status'  => 'fail',
                'message' => 'There is no marker within this ' . env('MARKER_RANGE',10000) . 'm range.'
            ], 500);
        }
        
        return response()->json([
            'status' => 'success',
            'data'   => $nearest_markers
        ], 200);
    }

    /**
     * get marker visited user
     * @param  MarkerVisitRequest $request
     * @param  Models\User        $user
     * @return json
     */
    public function markerVisit(MarkerVisitRequest $request, Models\User $user)
    {
        
        $marker        = Models\Marker::find($request->marker_id);
        
        \DB::beginTransaction();
        
        if ($marker->user_id != $user->id) {

            $marker_points = env('DEFAUTL_COMMON_VISIT_POINTS', 100);

            Models\MarkerVisit::create([
                'user_id'   => $user->id,
                'marker_id' => $request->marker_id,
            ]);
            
            if ($request->marker_type == "treasure-chest") {
                
                $marker_points = $marker->marker_points;
                $marker->delete(); 
            }
            
            Models\UserPoint::whereUserId($user->id)->increment('total_points', $marker_points);
            
            \DB::commit();    
            
            return response()->json([
                'status' => 'success',
            ], 200);
        }
        
        return response()->json([
            'status'  => 'fail',
            'message' => 'You are not authorized for this.'
        ], 500);
    }

    /**
     * set flag for event marker
     * @param  EventMarkerFlag $request
     * @param  Models\User     $user
     * @return json
     */
    public function eventMarkerFlag(EventMarkerFlagRequest $request, Models\User $user)
    {
        
        $marker = Models\Marker::find($request->marker_id);
        dd($marker);
        if ($marker->user_id != $user->id) {
            
            $marker->status = 2;
            $marker->save(); 
            
            return response()->json([
                'status' => 'success'
            ]);
        }

        return response()->json([
            'status'  => 'fail',
            'message' => 'You are not authorized for this.'
        ], 500);
    }

    /**
     * list of all visited marker of user
     * @param  Models\User $user 
     * @return json            
     */
    public function listOfVisitedMarker(Models\User $user)
    {
        
        $marker        = Models\Marker::select('id')->whereStatus(1)->whereUserId($user->id)->lists('id');
        
        $visitedMarker = Models\MarkerVisit::whereIn('marker_id', $marker)->with([
            'user' => function($query){
                $query->select('id', 'name', 'email');
            }, 
            'marker' => function($query){
                $query->select('id', 'marker_type_id', 'name', 'lat', 'lon');
            }
        ])->get()->toArray();
        
        return response()->json([
            'status' => 'success',
            'data'   => $visitedMarker
        ], 200);
    }
}