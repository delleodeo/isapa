<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'shop_name',
        'description',
        'address',
        'contact_number',
        'photo_path',
        'verification_status',
        'business_permit_path',
        'owner_id_path',
        'proof_of_address_path',
        'rejection_reason',
        'verified_at',
    ];

    /**
     * Check if shop is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'approved';
    }

    /**
     * Check if shop is pending verification.
     */
    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if shop is rejected.
     */
    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Get the owner of the shop.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the services offered by the shop.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the orders for the shop.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
