<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'table_number',
        'qr_code',
        'capacity',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Generate QR code token
    public function generateQrCode(): string
    {
        $token = bin2hex(random_bytes(16));
        $this->update(['qr_code' => $token]);
        return $token;
    }

    // Get QR URL
    public function getQrUrl(): string
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        return "{$frontendUrl}/order/{$this->outlet_id}/{$this->id}?token={$this->qr_code}";
    }
}
