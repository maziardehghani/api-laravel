<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::query()->paginate(3);

        return $this->SuccessResponse('list of categories' , 200 ,
        [
            'data' => BrandResource::collection($categories),
            'links'=> BrandResource::collection($categories)->response()->getData()->links,
            'meta'=> BrandResource::collection($categories)->response()->getData()->meta
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
                'parent_id' => 'required|integer|',
                'description' => 'nullable'
            ]);

        if ($validate->fails())
        {
            return $this->ErrorResponse($validate->getMessageBag() , 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'description' => $request->description
        ]);

        return $this->SuccessResponse('creat category',201,new CategoryResource($category));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->SuccessResponse('show a category' , 200 , new CategoryResource($category));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validate = Validator::make($request->all() ,
            [
                'name' => 'required',
                'parent_id' => 'required|integer|',
                'description' => 'nullable'
            ]);

        if ($validate->fails())
        {
            return $this->ErrorResponse($validate->getMessageBag() , 422);
        }

        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'description' => $request->description
        ]);

        return $this->SuccessResponse('creat category',201,new CategoryResource($category));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->SuccessResponse('category deleted' , 200 , new CategoryResource($category));
    }
    public function children(Category $category)
    {
        return $this->SuccessResponse('category children',200,new CategoryResource($category->load('children')));
    }
    public function products(Category $category)
    {
        return $this->SuccessResponse("products of category" , 200 ,
        new CategoryResource($category->load('products'))
        );


    }
}
