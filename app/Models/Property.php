<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price',
        'location',
        'type',
        'bedrooms',
        'bathrooms',
        'area_sqm',
        'status',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function features()
    {
        return $this->hasMany(PropertyFeature::class);
    }

    
    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }
}