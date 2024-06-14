<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Lending;
use App\Models\Stuff;
use App\Models\StuffStock;
use Illuminate\Http\Request;

class LendingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $getLending = Lending::with('stuff', 'user', 'restorations')->get();

            return ApiFormatter::sendResponse(200, 'Succesfully Get All Lending Data', $getLending);
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
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
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'stuff_id' => 'required',
                'date_time' => 'required',
                'name' => 'required',
                'user_id' => 'required',
                'notes' => 'required',
                'total_stuff' => 'required',
            ]);

            $createLending = Lending::create([
                'stuff_id' => $request->stuff_id,
                'date_time' => $request->date_time,
                'name' => $request->name,
                'user_id' => $request->user_id,
                'notes' => $request->notes,
                'total_stuff' => $request->total_stuff,
            ]);

            $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
            $updateStock = $getStuffStock->update([
                'total_available' => $getStuffStock['total_available'] - 
                $request->total_stuff,
            ]);
             
            return ApiFormatter::sendResponse(200, 'Successfully Create A Lending Data', $createLending);
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $getLending = Lending::where('id', $id)->with('stuff', 'user')->first();
             if (!$getLending) {
                return ApiFormatter::sendResponse(404, false, 'Data Lending not Found');
             } else {
                return ApiFormatter::sendResponse(200, true, 'Successfully Get A Lending Data', $getLending);
             }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
        }
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
         try {
             $getLending = Lending::find($id);
 
             if ($getLending) {
                 $this->validate($request, [
                     'stuff_id' => 'required',
                     'date_time' => 'required',
                     'name' => 'required',
                     'user_id' => 'required',
                     'notes' => 'required',
                     'total_stuff' => 'required',
                 ]);
 
                 $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();// get stock berdasarkan request stuff id
                 $getCurrentStock = StuffStock::where('stuff_id', $getLending['stuff_id'])->first();// get stock berdasarkan id lending

                 if ($request->stuff_id == $getCurrentStock['stuff_id']) {
                    $updateStock = $getCurrentStock->update([ 'total_available' => $getCurrentStock['total_available'] + $getLending['total_stuff'] - $request->total_stuff, ]);
                 } else {
                    $updateStock = $getCurrentStock->update([
                        'total_available' => $getCurrentStock['total_available'] + $getLending['total_stuff'],
                    ]);// total available lama dijumlahkan dengan total pinjaman barang yang lama
                    $updateStock = $getStuffStock->update([
                        'total_available' => $getStuffStock['total_available'] - $request['total_stuff'],
                    ]);// total available baru dikurangi dengan total pinjaman baru
                 }

                 $updateLending = $getLending->update([
                    'stuff_id' => $request->stuff_id,
                    'date_time' => $request->date_time,
                    'name' => $request->name,
                    'user_id' => $request->user_id,
                    'notes' => $request->notes,
                    'total_stuff' => $request->total_stuff,
                 ]);

                 $getUpdateLending = Lending::where('id', $id)->with('stuff', 'user', 'restorations')->first();

                 return ApiFormatter::sendResponse(200, 'Successfully Update A Lending Data', $getUpdateLending);
             }
         } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, $err->getMessage());
         }
     }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $getLending = Lending::find();

        } catch (\Exception $err) {
            // Tangani kesalahan
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function _construct()
{
    $this->middleware('auth:api');
}
}