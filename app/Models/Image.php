<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['url']; // 其他可填充的属性

    public function imageable()
    {
        return $this->morphTo();
    }
}
