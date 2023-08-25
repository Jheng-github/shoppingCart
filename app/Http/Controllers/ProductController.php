<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductResourceCollection;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{

    const FOLDER_TMP_IMAGES = 'tmp';
    const FOLDER_IMAGES = 'images';
    /**
     * 可上傳images圖片一張 ,
     * 名字,商品敘述,價格,數量
     */
    public function store(StoreProductRequest $request)
    {

        $validate = $request->validated();

        DB::beginTransaction();
        try {
            $currentDate = Carbon::now()->format('ymd');

            $productNumber = Product::where('product_number', 'like', "$currentDate%")->lockForUpdate()->count();

            $newProductNumberNumber = sprintf("%06d", ($productNumber + 1));

            $validate['product_number'] = $currentDate . $newProductNumberNumber;

            $product =  Product::create($validate);

            $this->addImage($request, self::FOLDER_IMAGES, $product);

            DB::commit();

            return ProductResource::make($product->load('images'));
        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
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
    public function update(Product $product, Request $request)
    {
        $validate = $this->validate($request, [
            'name' => ['required', 'string', 'max:15'],
            'price' => ['required', 'integer'],
            'stock' => ['required', 'integer', 'min:0'],
            'depiction' => ['required', 'string'],
            'images' => ['required', 'array', 'max:6', 'min:1'],
        ]);

        $validate['product_number'] = $product->product_number;

        DB::beginTransaction();

        try {

            $product->lockforupdate();

            //取得已經在資料庫裡面的舊圖片
            $getOldImage = $product->images->pluck('url')->toArray();

            $images = $request->input('images');

            $image = [];

            $requestOldImage = [];

            foreach ($images as $image) {

                //原有圖片再次request,代表沒有要刪除
                if ($image['id'] !== 0) {

                    $oldImage = $product->images()->where('id', $image['id'])->first()->toArray();

                    abort_if(!$oldImage, Response::HTTP_BAD_REQUEST, "圖片不屬於該商品");

                    $requestOldImage[] = $oldImage['url'];
                }

                //id = 0, 新增使用者上傳的圖片
                if ($image['id'] == 0) {
                    $sourcePath = base_path($image['tmpPath']); //抓到檔案在tmp的位置

                    $getName = basename($image['tmpPath']); //取得檔案名字

                    $destinationPath = base_path('storage/app/public/images/' . $getName); // 圖片要放的位置

                    File::move($sourcePath, $destinationPath); //直接搬檔案

                    $product->images()->create([ //接著把路徑塞進去資料庫
                        'url' =>  'images' . $image['tmpPath']
                    ]);
                }
            }

            //圖片若有重複則會移除陣列,重複代表不刪除,沒重複會以第一參數陣列舊圖片留下,代表要刪除(已經在第二參數陣列沒input了)
            $imageToDeletes = array_diff($getOldImage, $requestOldImage);

            foreach ($imageToDeletes as $imageToDelete) {
                // 刪除資料庫中的圖片
                $product->images()->where('url', $imageToDelete)->delete();
                // 從資料夾中刪除圖片
                Storage::delete($imageToDelete);
            }

            // 更新Product
            $product->update($validate);

            DB::commit();

            return response()->json(
                ['message' => '商品更改成功'],
                201
            );
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

    /**
     * 新增圖片到tmp的位置,update圖片時,圖片需要先傳入這個位置
     */
    public function addImageToTmp(Request $request)
    {

        $this->validate($request, [
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image'],
        ]);

        $this->addImage($request, self::FOLDER_TMP_IMAGES);

        return response()->json(
            [
                'data' => '上傳成功',
            ],
            201
        );
    }

    public function addImage($request, $fileInputName,  Product $product = null)
    {
        if ($fileInputName == self::FOLDER_IMAGES) {
            foreach ($request->file('images') as $image) {
                $product->images()->create([
                    'url' =>  $image->store($fileInputName)
                ]);
            }
        }

        if ($fileInputName == self::FOLDER_TMP_IMAGES) {
            foreach ($request->file('images') as $image) {
                $image->store($fileInputName); // 將文件存儲到 tmp 資料夾
            }
        }
    }
}
