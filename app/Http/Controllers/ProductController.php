<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductResourceCollection;
use App\Models\Product;
use App\Services\ImageService;
use App\Services\ProductService;

class ProductController extends Controller
{

    private $productService;
    private $imageService;

    public function __construct(ProductService $productService, ImageService $imageService)
    {
        $this->productService = $productService;
        $this->imageService = $imageService;
    }


    /**
     * 可上傳images圖片一張 ,
     * 名字,商品敘述,價格,數量
     */
    public function store(StoreProductRequest $request, Product $product)
    {

        $validate = $request->validated();

        $product = $this->productService->createProduct($validate);

        $this->imageService->addImage($request, Constants::FOLDER_IMAGES, $product);

        return ProductResource::make($product->load('images'));
    }

    public function index()
    {
        $getProducts = Product::latest()->with('images')->get();

        return ProductResourceCollection::make($getProducts);
    }

    public function show(Product $product)
    {

        return ProductResource::make($product->load('images'));
    }

    /**
     * images id = 0, 代表要新增的圖片,來源是tmp,須把要新增的圖片先上傳到tmp
     *  id = {id}, 代表已存在圖片id
     *  若帶過來圖片沒有原本的該商品的id 則代表刪掉圖片
     */
    public function update(Product $product, UpdateProductRequest $request)
    {
        $validate = $request->validated();

        $validate['product_number'] = $product->product_number;
        $getRequestImages =  $request->input('images');
        $this->productService->updateProduct($product, $request, $validate);

        $this->imageService->updateOrDeleteImage($product, $getRequestImages);

        return response()->json([
            'msg' => '商品更改成功'
        ], 201);
    }
}
