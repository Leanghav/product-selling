<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'phone',
        'email',
        'address',
        'balance',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}