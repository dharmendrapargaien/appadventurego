<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\Api\V1\CreateMarkerRequest;

use App\Models;

class MarkerController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $markers = Models\Marker::whereStatus(1)->orderBy('created_at', 'desc')->get();
        
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
    public function store(CreateMarkerRequest $request)
    {

        \DB::beginTransaction();

        //create new user
         $marker_data = [
            'user_id'        => $request->user_id,
            'marker_type_id' => Models\MarkerType::select('id')->whereTypeSlug($request->marker_type)->first()['id'],
            'name'           => $request->name,
            'description'    => $request->description,
            'lat'            => $request->lat,
            'long'           => $request->long,
            'marker_points'  => $request->marker_points,
            'marker_stars'   => $request->marker_stars,
        ];

        //if marker type is event then we need data and time
        if($request->has('marker_type') && $request->get('marker_type') == 'event') {
            
            $marker_data['marker_date']    = $request->marker_date;
            $marker_data['marker_time']    = $request->marker_time;    
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
    public function markerTypes($id)
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
}
