<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Service;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Get all registered shops.
     */
    public function getShops()
    {
        $shops = Shop::with('owner')
            ->withCount('services')
            ->where('verification_status', 'approved')
            ->get();
        return response()->json($shops);
    }

    /**
     * Get details of a specific shop.
     */
    public function getShopDetails($id)
    {
        $shop = Shop::with('owner')
            ->where('id', $id)
            ->where('verification_status', 'approved')
            ->first();
        
        if (!$shop) {
            return response()->json([
                'message' => 'Shop not found or not available'
            ], 404);
        }

        return response()->json($shop);
    }

    /**
     * Get services of a specific shop.
     */
    public function getServices($shopId)
    {
        // Verify shop exists and is approved
        $shop = Shop::where('id', $shopId)
            ->where('verification_status', 'approved')
            ->first();
        
        if (!$shop) {
            return response()->json([
                'message' => 'Shop not found or not available',
                'services' => []
            ], 404);
        }

        $services = Service::where('shop_id', $shopId)
            ->with(['category', 'shop'])
            ->get();
        
        return response()->json($services);
    }

    /**
     * Book a service.
     */
    public function bookService(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'service_id' => 'required|exists:services,id',
            'delivery_type' => 'required|in:pickup,delivery',
            'pickup_address' => 'required_if:delivery_type,delivery|nullable|string',
            'delivery_address' => 'required_if:delivery_type,delivery|nullable|string',
            'contact_number' => 'required|string',
            'payment_method' => 'required|in:online,cash',
        ]);

        // Verify shop is approved
        $shop = Shop::where('id', $request->shop_id)
            ->where('verification_status', 'approved')
            ->first();
            
        if (!$shop) {
            return response()->json([
                'message' => 'Shop is not available for booking',
            ], 403);
        }

        $service = Service::findOrFail($request->service_id);

        // Calculate delivery fee (50 pesos for home delivery)
        $deliveryFee = $request->delivery_type === 'delivery' ? 50.00 : 0.00;
        
        $totalAmount = $service->price;
        $adminRevenue = $totalAmount * 0.10; // 10% for admin
        $shopRevenue = $totalAmount * 0.90; // 90% for shop

        $orderData = [
            'customer_id' => $request->user()->id,
            'shop_id' => $request->shop_id,
            'service_id' => $request->service_id,
            'delivery_type' => $request->delivery_type,
            'contact_number' => $request->contact_number,
            'payment_method' => $request->payment_method,
            'total_amount' => $totalAmount,
            'delivery_fee' => $deliveryFee,
            'shop_revenue' => $shopRevenue,
            'admin_revenue' => $adminRevenue,
            'order_status' => 'pending',
        ];

        // Only add addresses for delivery type
        if ($request->delivery_type === 'delivery') {
            $orderData['pickup_address'] = $request->pickup_address;
            $orderData['delivery_address'] = $request->delivery_address;
        }

        $order = Order::create($orderData);

        // Create notification for shop owner
        $shop = Shop::findOrFail($request->shop_id);
        $deliveryTypeText = $request->delivery_type === 'pickup' ? 'Drop-off/Pick-up' : 'Home Delivery';
        Notification::create([
            'order_id' => $order->id,
            'user_id' => $shop->owner_id,
            'message' => "New {$deliveryTypeText} order received from {$request->user()->name}",
        ]);

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order->load(['service', 'shop']),
        ], 201);
    }

    /**
     * Get customer's orders.
     */
    public function getOrders(Request $request)
    {
        $orders = Order::where('customer_id', $request->user()->id)
            ->with(['service', 'shop'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Cancel an order (only if pending).
     */
    public function cancelOrder(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        // Only allow cancellation if order is still pending
        if ($order->order_status !== 'pending') {
            return response()->json([
                'message' => 'Order cannot be cancelled. It has already been approved.',
            ], 400);
        }

        // Notify shop owner about cancellation
        $shop = Shop::findOrFail($order->shop_id);
        Notification::create([
            'order_id' => $order->id,
            'user_id' => $shop->owner_id,
            'message' => "Order #{$order->tracking_number} has been cancelled by {$request->user()->name}",
        ]);

        $order->delete();

        return response()->json([
            'message' => 'Order cancelled successfully',
        ]);
    }

    /**
     * Track order by tracking number.
     */
    public function trackOrder($trackingNumber)
    {
        $order = Order::where('tracking_number', $trackingNumber)
            ->with(['service', 'shop', 'customer'])
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json($order);
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'house_number' => 'sometimes|nullable|string|max:100',
            'street' => 'sometimes|nullable|string|max:255',
            'barangay' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
            'province' => 'sometimes|nullable|string|max:255',
            'postal_code' => 'sometimes|nullable|string|max:10',
        ]);

        $user = $request->user();
        $user->update($request->only([
            'name', 
            'phone', 
            'house_number', 
            'street', 
            'barangay', 
            'city', 
            'province', 
            'postal_code'
        ]));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Get customer dashboard stats.
     */
    public function getDashboard(Request $request)
    {
        $customerId = $request->user()->id;

        $stats = [
            'active_orders' => Order::where('customer_id', $customerId)
                ->whereNotIn('order_status', ['delivered', 'picked_up'])
                ->count(),
            'completed_orders' => Order::where('customer_id', $customerId)
                ->whereIn('order_status', ['delivered', 'picked_up'])
                ->count(),
            'total_spent' => Order::where('customer_id', $customerId)
                ->whereIn('order_status', ['delivered', 'picked_up'])
                ->sum('final_amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Get customer notifications.
     */
    public function getNotifications(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json([
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully',
        ]);
    }

    /**
     * Process online payment for an order.
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:gcash,paymaya,card',
            'amount' => 'required|numeric|min:0',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        // Verify order is in processing or pending_payment status
        if (!in_array($order->order_status, ['processing', 'pending_payment'])) {
            return response()->json([
                'message' => 'Order is not ready for payment. Shop must process the order first.',
            ], 400);
        }

        // Verify order has weight (shop has weighed it)
        if (!$order->weight_kg) {
            return response()->json([
                'message' => 'Order weight has not been set yet. Please wait for shop to process your laundry.',
            ], 400);
        }

        // Verify order is set for online payment
        if ($order->payment_method !== 'online') {
            return response()->json([
                'message' => 'This order is not set for online payment.',
            ], 400);
        }

        // Verify order hasn't been paid yet
        if ($order->paid_at) {
            return response()->json([
                'message' => 'This order has already been paid.',
            ], 400);
        }

        // Verify amount matches final amount
        if (abs($request->amount - ($order->final_amount ?? $order->total_amount)) > 0.01) {
            return response()->json([
                'message' => 'Payment amount does not match order total.',
            ], 400);
        }

        // TODO: Integrate with PayMongo API here
        // For now, we'll just mark the order as paid
        // In production, you would create a payment intent with PayMongo,
        // redirect user to payment page, and handle webhook confirmation

        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'paymongo_payment_method' => $request->payment_method,
            'order_status' => 'processing', // Move back to processing after payment
        ]);

        // Create notification for shop owner
        $shop = Shop::findOrFail($order->shop_id);
        Notification::create([
            'order_id' => $order->id,
            'user_id' => $shop->owner_id,
            'message' => "Payment received! Order #{$order->tracking_number} paid ₱" . number_format($order->final_amount, 2) . " via {$request->payment_method}",
        ]);
        
        // Create notification for customer
        Notification::create([
            'order_id' => $order->id,
            'user_id' => $order->customer_id,
            'message' => "Payment successful! Your order #{$order->tracking_number} is now being processed.",
        ]);

        return response()->json([
            'message' => 'Payment processed successfully',
            'order' => $order->fresh()->load(['service', 'shop']),
        ]);
    }

    /**
     * Update customer password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        // Check if user logged in via OAuth (has google_id or facebook_id)
        $isOAuthUser = !empty($user->google_id) || !empty($user->facebook_id);

        if ($isOAuthUser) {
            // OAuth users can set password without current password
            $request->validate([
                'new_password' => 'required|min:8|confirmed',
            ]);
        } else {
            // Regular users must provide current password
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect',
                ], 400);
            }
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }
}
