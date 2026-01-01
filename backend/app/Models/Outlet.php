<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'business_type',
        'address',
        'phone',
        'enable_qr_order',
        'is_active',
    ];

    protected $casts = [
        'enable_qr_order' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class);
    }

    // Business type checks
    public function isRetail(): bool
    {
        return $this->business_type === 'retail';
    }

    public function isMinimarket(): bool
    {
        return $this->business_type === 'minimarket';
    }

    public function isFnb(): bool
    {
        return $this->business_type === 'fnb';
    }
}
