<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\Api\V1\CreateMarkerRequest;
use App\Http\Requests\Api\V1\NearestMarkerRequest;

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
        $markers = Models\Marker::whereUserId($user->id)->whereStatus(1)->orderBy('created_at', 'desc')->get();
        
        if ($markers->count() == 0) {

            return response()->json([
                'status'  => 'fail',
                'message' => 'Data not found'
            ], 500);
        }
        
        return response()->json([
            'status' => 'success',
            'data'   => $markers
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markerTypes()
    {

        $marker_types = Models\MarkerType::select('type_slug', 'name', 'description','marker_points', 'marker_stars')->where('marker_for' , '!=' , 0)->whereStatus(1)->orderBy('name')->get();
        
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

        $nearest_markers = Models\Marker::select('id','lat', 'lon', \DB::raw("( 3959 * acos ( cos ( radians($request->lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($request->lon) ) + sin ( radians($request->lat) ) * sin( radians( lat ) ) ) ) AS distance"))->having('distance','<',(0.621371 * env('MARKER_RANGE',10000)))->where('user_id', '<>', $user->id)->get();
        
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
}
