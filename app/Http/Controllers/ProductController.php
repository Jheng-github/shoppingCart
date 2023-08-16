<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * 可上傳images圖片一張 ,
     * 名字,商品敘述,價格,數量
     */
    public function store(Request $request)
    {
        $validate = $this->validate($request, [
            'name' => ['required', 'string', 'max:15'],
            'price' => ['required', 'integer'],
            'stock' => ['required', 'integer', 'min:0'],
            'depiction' => ['required', 'string'],
            'images' => ['required', 'array', 'max:6', 'min:1'],
            'images.*' => ['image'],
        ]);

        DB::beginTransaction();
        try {
            $currentDate = Carbon::now()->format('ymd');

            $productNumber = Product::where('product_number', 'like', "$currentDate%")->lockForUpdate()->count();

            $newProductNumberNumber = sprintf("%06d", ($productNumber + 1));

            $validate['product_number'] = $currentDate . $newProductNumberNumber;

            $product =  Product::create($validate);

            $this->addImage($request, $product);

            DB::commit();

            return ProductResource::make($product->load('images'));
        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    public function index()
    {
        $getProducts = Product::latest()->get();    

        return ProductCollection::make($getProducts->load('images'));
    }

    public function addImage($request, Product $product)
    {

        foreach ($request->file('images') as $image) {
            $product->images()->create([
                'url' =>  $image->store('clothes')
            ]);
        }
    }
}
