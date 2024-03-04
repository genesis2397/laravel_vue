<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name',
        'description',
        'video_link',
    ];
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('l jS \\of F Y h:i:s A');
    }
}
