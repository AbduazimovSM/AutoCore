<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reference;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = ['name','barcode','sku','category_id','unit_id','brand_id','image','min_quantity','description','status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function category(){
        return $this->belongsTo(Reference::class, 'category_id');
    }

    public function unit(){
        return $this->belongsTo(Reference::class, 'unit_id');
    }

    public function brand(){
        return $this->belongsTo(Reference::class, 'brand_id');
    }

}
