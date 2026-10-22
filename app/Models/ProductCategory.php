<?php

namespace App\Models;

use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use LogsContentActivity;

    protected $fillable = ['name'];

    public function products(): HasMany
    {
        return $this->hasMany(Service::class, 'product_category', 'name')->withTrashed()->products();
    }
}
