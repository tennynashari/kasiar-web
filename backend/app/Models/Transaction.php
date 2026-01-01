<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_no',
        'outlet_id',
        'business_type',
        'user_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'payment_details',
        'notes',
        'table_id',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_details' => 'array',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class);
    }

    // Generate transaction number
    public static function generateTransactionNo(int $outletId): string
    {
        $date = now()->format('Ymd');
        $outlet = str_pad($outletId, 3, '0', STR_PAD_LEFT);
        $lastTransaction = self::where('outlet_id', $outletId)
            ->whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastTransaction ? (int) substr($lastTransaction->transaction_no, -4) + 1 : 1;
        $sequenceStr = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return "TRX{$date}{$outlet}{$sequenceStr}";
    }
}
