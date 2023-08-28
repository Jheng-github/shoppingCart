<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class ImageService
{

    public function addImage($request, $fileInputName, Product $product = null)
    {
        if ($fileInputName == Constants::FOLDER_IMAGES) {
            foreach ($request->file('images') as $image) {
                $product->images()->create([
                    'url' =>  $image->store($fileInputName)
                ]);
            }
        }

        if ($fileInputName == Constants::FOLDER_TMP_IMAGES) {
            foreach ($request->file('images') as $image) {
                $image->store($fileInputName);
            }
        }
    }

    public function updateOrDeleteImage($product, $images)
    {
        //取得已經在資料庫裡面的舊圖片
        $getOldImage = $product->images->pluck('url')->toArray();

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
    }
}
