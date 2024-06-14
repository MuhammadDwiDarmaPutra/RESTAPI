<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\StuffStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StuffStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $getStuffStock = StuffStock::with('stuff')->get();

            return ApiFormatter::sendResponse(200, true, 'Succesfully Get All Stuff Stock Data', $getStuffStock);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
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
        $validator = Validator::make
        ($request->all(), [
            'stuff_id' => 'required',
            'total_available' => 'required',
            'total_defect' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
              'success' => false,
              'message' => 'Semua kolom wajib disi!',
                'data' => $validator->errors()
            ],400);
        } else{
            $stock = StuffStock::updateOrCreate([
                'stuff_id' => $request->input('stuff_id')
            ],[
                'total_available' => $request->input('total_available'),
                'total_defac' => $request->input('total_defac')
            ]);


            if($stock) {
                return response()->json([
                 'success' => true,
                 'message' => 'Barang berhasil ditambahkan',
                    'data' => $stock
                ],200);
            } else{
                return response()->json([
                'success' => false,
                'message' => 'Barang gagal ditambahkan',
                ],400);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $stock = StuffStock::with('stuff')->find($id);

            return response()->json([
                'success' => true, 
                'message' => 'Lihat semua stock barang dengan id ' . $id,
                'data' => $stock
            ], 200);
        } catch(\Throwable $th){
            return response() -> json([
                'success' => false,
                'message' => 'dara dengan id' . $id .'tidak ditemukan'
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function edit(StuffStock $stuffStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $stock = StuffStock::with('stuff')->find($id);

            $stuff_id = ($request->stuff_id) ? $request->stuff_id : $stock->stuff_id;
            $total_available = ($request->total_available) ? $request->total_available : $stock->total_available;
            $total_defect = ($request->total_defect) ? $request->total_defect : $stock->total_defect;

            if ($stock) {
                $stock->update([
                    'stuff_id' => $stuff_id,
                    'total_available' => $total_available,
                    'total_defac' => $total_defect
                ]);

                return response()->json([
                  'success' => true,
                  'message' => 'Barang berhasil diubah',
                    'data' => $stock
                ],200);
            } else{
                return response()->json([
                    'success' => false,
                    'message' => 'Proses gagal',
                  ],400);
            }
        } catch(\Throwable $th){
            return response()->json([
              'success' => false,
              'message' => 'Proses gagal! data dengan id '.$id.' tidak ditemukan',
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $stuffStock = stuffStock::findOrFail($id);
    
            $stuffStock->delete();
    
            return response()->json([
             'success' => true,
             'message' => 'Barang Hapus Data dengan id' . $id,
                'data' => $stuffStock
            ],200);
        } catch(\Throwable $th){
            return response()->json([
            'success' => false,
            'message' => 'Proses gagal! data dengan id '.$id.' tidak ditemukan',
            ],400);
        }
    }

    public function addStock(Request $request, $id)
    {
        try {
             $getStuffStock = StuffStock::find($id);

             if (!$getStuffStock) {
                return ApiFormatter::sendResponse(400, false, 'Data Stuff Stock Not Found');
             } else {
                $this->validate($request, [
                    'total_available' => 'required',
                    'total_defac' => 'required',
                ]);

                $addStock = $getStuffStock->update([
                    'total_available' => $getStuffStock['total_available'] + $request->total_available,
                    'total_defac' => $getStuffStock['total_defac'] + $request->total_defac,
                ]);

                if ($addStock) {
                    $getStuffAdded = StuffStock::where('id', $id)->with('stuff')->first();

                    return ApiFormatter::sendResponse(200, true, 'Succesfully Add A Stock Of Stuff Stock Data', $getStuffAdded);
                }
             }
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
    }

    public function subStock(Request $request, $id)
    {
        try {
             $getStuffStock = StuffStock::find($id);

             if (!$getStuffStock) {
                return ApiFormatter::sendResponse(400, false, 'Data Stuff Stock Not Found');
             } else {
                $this->validate($request, [
                    'total_available' => 'required',
                    'total_defac' => 'required',
                ]);

                $isStockAvailable = $getStuffStock->update['total_available'] - $request->total_available;
                $isStockDefac = $getStuffStock->update['total_defac'] - $request->total_defac;

                if ($isStockAvailable < 0 || $isStockDefac < 0) {
                    return ApiFormatter::sendResponse(400, true, 'Substraction Stock Cant Less Than A Stock Stored');
                } else {
                    $subStock = $getStuffStock->update([
                        'total_available' => $isStockAvailable,
                        'total_defac' => $isStockDefac,
                    ]);

                    if ($subStock) {
                        $getStockSub = StuffStock::where('id', $id)->with('stuff')->first();

                        return ApiFormatter::sendResponse(200, true, 'Succesfully Sub A Stock Of StuFf Stock Data', $getStockSub);
                    }
                }
             }
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
    }

    public function _construct()
{
    $this->middleware('auth:api');
}
}