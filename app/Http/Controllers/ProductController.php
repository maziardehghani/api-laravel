<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Product_Image;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::query()->paginate(2);
        return $this->SuccessResponse('list of products', 200 ,
            [
                'products' => ProductResource::collection($products->load('images')),
                'links' => ProductResource::collection($products)->response()->getData()->links,
                'meta' => ProductResource::collection($products)->response()->getData()->meta
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
        $validator = Validator::make($request->all() ,
        [
            'name' => 'required|string|max:130|min:3',
            'brand_id' => 'required|integer|exists:App\Models\Brand,id',
            'category_id' => 'required|integer|exists:App\Models\Category,id',
            'primary_image' => 'required|max:100000|mimes:jpg,jpeg,png',
            'image.*' =>'nullable|max:100000|mimes:jpg,jpeg,png',
            'description' => 'required|string|max:250',
            'price' => 'required|integer',
            'delivery_amount' => 'required|integer',
            'quantity' => 'required|integer',
        ]
        );

        if ($validator->fails())
        {
            return $this->ErrorResponse($validator->getMessageBag(),422);
        }


        DB::beginTransaction();
            $primary_image = $request->primary_image;
            $name = 'product'.Carbon::now()->microsecond;
            $extension = strtolower($primary_image->getClientOriginalExtension());
            $filename = $name.'.'.$extension;
            $dir = 'image/products';
            Storage::putFileAs($dir ,$primary_image ,$filename);

            if ($request->has('image'))
            {
                $images_filename=[];
                foreach ($request->image as $images)
                {
                    $name = 'gallery'.Carbon::now()->microsecond;
                    $extension = strtolower($primary_image->getClientOriginalExtension());
                    $gallery = $name.'.'.$extension;
                    $dir = 'image/products/gallery';
                    Storage::putFileAs($dir ,$images ,$gallery);
                    array_push($images_filename,$gallery);
                }
            }


        $product = Product::create([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'image' => $filename,
            'description' => $request->description,
            'price' => $request->price,
            'delivery_amount' => $request->delivery_amount,
            'quantity' => $request->quantity,
        ]);


            if ($request->has('image'))
            {
                foreach ($images_filename as $image_filename)
                {
                    Product_Image::create([
                        'product_id' => $product->id,
                        'image' => $image_filename
                    ]);
                }

            }

            DB::commit();

        return $this->SuccessResponse('product created' , 201 , new ProductResource($product));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return  $this->SuccessResponse("products info" , 200 ,new ProductResource($product->load('images')));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {

        $validator = Validator::make($request->all() ,
            [
                'name' => 'required|string|max:130|min:3',
                'brand_id' => 'required|integer|exists:App\Models\Brand,id',
                'category_id' => 'required|integer|exists:App\Models\Category,id',
                'primary_image' => 'nullable|max:100000|mimes:jpg,jpeg,png',
                'image.*' =>'nullable|max:100000|mimes:jpg,jpeg,png',
                'description' => 'required|string|max:250',
                'price' => 'required|integer',
                'delivery_amount' => 'required|integer',
                'quantity' => 'required|integer',
            ]
        );

        if ($validator->fails())
        {
            return $this->ErrorResponse($validator->getMessageBag(),422);
        }


        DB::beginTransaction();
        if ($request->has('primary_image'))
        {
            $primary_image = $request->primary_image;
            $name = 'product'.Carbon::now()->microsecond;
            $extension = strtolower($primary_image->getClientOriginalExtension());
            $filename = $name.'.'.$extension;
            $dir = 'image/products';
            Storage::putFileAs($dir ,$primary_image ,$filename);  }

        if ($request->has('image'))
        {
            $images_filename=[];
            foreach ($request->image as $images)
            {
                $name = 'gallery'.Carbon::now()->microsecond;
                $extension = strtolower($primary_image->getClientOriginalExtension());
                $gallery = $name.'.'.$extension;
                $dir = 'image/products/gallery';
                Storage::putFileAs($dir ,$images ,$gallery);
                array_push($images_filename,$gallery);
            }
        }


        $product->update([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'image' => $request->has('image') ? $filename : $product->image,
            'description' => $request->description,
            'price' => $request->price,
            'delivery_amount' => $request->delivery_amount,
            'quantity' => $request->quantity,
        ]);

        if ($request->has('image'))
        {
            foreach ($images_filename as $image_filename)
            {
                Product_Image::create([
                    'product_id' => $product->id,
                    'image' => $image_filename
                ]);
            }

        }
        DB::commit();
        return $this->SuccessResponse('product created' , 201 , new ProductResource($product));

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
}
