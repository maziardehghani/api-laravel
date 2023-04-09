<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;


class BrandController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

            $brands = Brand::query()->paginate(2);
            return $this->SuccessResponse('list of brands',200 ,
                [
                    'brands' => BrandResource::collection($brands),
                    'links' => BrandResource::collection($brands)->response()->getData()->links,
                    'meta' => BrandResource::collection($brands)->response()->getData()->meta,
                ]
            );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validate = Validator::make($request->all() ,
        [
            'name' => 'required',
            'display_name' => 'required|unique:brands'
        ]);

        if ($validate->fails())
        {
            return $this->ErrorResponse($validate->getMessageBag() , 422);
        }

        $brand = Brand::create([
            'name' => $request->name,
            'display_name' => $request->display_name
        ]);

        return $this->SuccessResponse('creat brand',201,new BrandResource($brand));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        return $this->SuccessResponse('show a brand' , 200 , new BrandResource($brand));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        $validate = Validator::make($request->all() ,
        [
            'name' => 'required',
            'display_name' => 'required',
        ]);

        if ($validate->fails())
        {
            return $this->ErrorResponse($validate->getMessageBag() , 422);
        }

        $brand->update(
            [
                'name' => $request->name,
                'display_name' => $request->display_name
            ]
        );
        return $this->SuccessResponse('update brand',200,new BrandResource($brand));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return $this->SuccessResponse('delete brand' , 200 ,new BrandResource($brand));
    }

    public function products(Brand $brand)
    {
        return $this->SuccessResponse('products of brand' ,200 , new BrandResource($brand->load('products')));
    }
}
