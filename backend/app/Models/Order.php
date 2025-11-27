<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'shop_id',
        'service_id',
        'delivery_type',
        'pickup_address',
        'delivery_address',
        'contact_number',
        'order_status',
        'payment_method',
        'payment_status',
        'paymongo_payment_id',
        'paymongo_source_id',
        'paymongo_payment_method',
        'payment_details',
        'paid_at',
        'total_amount',
        'delivery_fee',
        'shop_revenue',
        'admin_revenue',
        'weight_kg',
        'final_amount',
        'final_shop_revenue',
        'final_admin_revenue',
        'tracking_number',
        'approved_at',
        'processing_at',
        'pending_payment_at',
        'ready_for_pickup_at',
        'for_delivery_at',
        'delivered_at',
        'picked_up_at',
        'cancelled_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'shop_revenue' => 'decimal:2',
        'admin_revenue' => 'decimal:2',
        'weight_kg' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'final_shop_revenue' => 'decimal:2',
        'final_admin_revenue' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'processing_at' => 'datetime',
        'pending_payment_at' => 'datetime',
        'ready_for_pickup_at' => 'datetime',
        'for_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->tracking_number)) {
                $order->tracking_number = 'SPNR-' . strtoupper(Str::random(10));
            }
        });

        static::updating(function ($order) {
            // Automatically set timestamp when status changes
            if ($order->isDirty('order_status')) {
                $status = $order->order_status;
                $timestampField = $status . '_at';
                
                // Set timestamp for current status if field exists
                if (in_array($timestampField, $order->getFillable()) && !$order->$timestampField) {
                    $order->$timestampField = now();
                }
            }
        });
    }

    /**
     * Get the customer who placed the order.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the shop for the order.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the service for the order.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the notifications for the order.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
