<?php

namespace App\Services;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class ProductService
{

    public function createProduct($validate)
    {

        DB::beginTransaction();
        try {
            $currentDate = Carbon::now()->format('ymd');

            $productNumber = Product::where('product_number', 'like', "$currentDate%")->lockForUpdate()->count();

            $newProductNumberNumber = sprintf("%06d", ($productNumber + 1));

            $validate['product_number'] = $currentDate . $newProductNumberNumber;

            $product =  Product::create($validate);

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateProduct(Product $product, $request, $validate)
    {
        DB::beginTransaction();

        try {

            $product->lockforupdate();

            // 更新Product
            $product->update($validate);

            DB::commit();
        } catch (\Exception $e) {

            DB::rollback();
            return response()->json(
                [
                    'message' => '商品更改失敗',
                    'error' => $e->getMessage()
                ],
                400
            );
        }
    }
}
