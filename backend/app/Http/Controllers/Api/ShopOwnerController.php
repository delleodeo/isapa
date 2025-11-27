<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Service;
use App\Models\Order;
use App\Models\Notification;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ShopOwnerController extends Controller
{
    /**
     * Get shop owner dashboard stats.
     */
    public function getDashboard(Request $request)
    {
        $shop = Shop::where('owner_id', $request->user()->id)->first();

        if (!$shop) {
            return response()->json([
                'message' => 'No shop found for this owner',
            ], 404);
        }

        // Get period parameter (default to 'all')
        $period = $request->query('period', 'all');
        
        // Base query for completed orders (delivered or picked_up)
        $baseQuery = Order::where('shop_id', $shop->id)
            ->whereIn('order_status', ['delivered', 'picked_up']);

        // Apply date filters based on period
        $query = clone $baseQuery;
        switch ($period) {
            case 'daily':
                $query->whereDate('updated_at', today());
                break;
            case 'weekly':
                $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('updated_at', now()->month)
                      ->whereYear('updated_at', now()->year);
                break;
            case 'yearly':
                $query->whereYear('updated_at', now()->year);
                break;
        }

        $stats = [
            'total_revenue' => $baseQuery->sum(DB::raw('COALESCE(final_shop_revenue, shop_revenue)')),
            'period_revenue' => $query->sum(DB::raw('COALESCE(final_shop_revenue, shop_revenue)')),
            'total_customers' => Order::where('shop_id', $shop->id)
                ->distinct('customer_id')
                ->count('customer_id'),
            'completed_orders' => $baseQuery->count(),
            'period_orders' => $query->count(),
            'pending_orders' => Order::where('shop_id', $shop->id)
                ->where('order_status', 'pending')
                ->count(),
            
            // Daily sales - filtered by period
            'daily_sales' => $this->getDailySales($shop->id, $period),
            
            // Monthly sales - filtered by period
            'monthly_sales' => $this->getMonthlySales($shop->id, $period),
            
            // Top services - filtered by period
            'top_services' => $this->getTopServices($shop->id, $period),
        ];

        return response()->json($stats);
    }

    /**
     * Get daily sales filtered by period.
     */
    private function getDailySales($shopId, $period)
    {
        $query = Order::where('shop_id', $shopId)
            ->whereIn('order_status', ['delivered', 'picked_up']);

        switch ($period) {
            case 'daily':
                $query->whereDate('updated_at', today());
                break;
            case 'weekly':
                $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('updated_at', now()->month)
                      ->whereYear('updated_at', now()->year);
                break;
            case 'yearly':
                $query->whereYear('updated_at', now()->year);
                break;
            default:
                $query->whereBetween('updated_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()]);
        }

        return $query->selectRaw('DATE(updated_at) as date, SUM(COALESCE(final_shop_revenue, shop_revenue)) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Get monthly sales filtered by period.
     */
    private function getMonthlySales($shopId, $period)
    {
        $query = Order::where('shop_id', $shopId)
            ->whereIn('order_status', ['delivered', 'picked_up']);

        switch ($period) {
            case 'daily':
                $query->whereDate('updated_at', today());
                break;
            case 'weekly':
                $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('updated_at', now()->month)
                      ->whereYear('updated_at', now()->year);
                break;
            case 'yearly':
                $query->whereYear('updated_at', now()->year);
                break;
            default:
                $query->whereYear('updated_at', now()->year);
        }

        return $query->selectRaw('MONTH(updated_at) as month, SUM(COALESCE(final_shop_revenue, shop_revenue)) as revenue, COUNT(*) as orders')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    /**
     * Get top services filtered by period.
     */
    private function getTopServices($shopId, $period)
    {
        $query = Order::where('shop_id', $shopId)
            ->whereIn('order_status', ['delivered', 'picked_up']);

        switch ($period) {
            case 'daily':
                $query->whereDate('updated_at', today());
                break;
            case 'weekly':
                $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('updated_at', now()->month)
                      ->whereYear('updated_at', now()->year);
                break;
            case 'yearly':
                $query->whereYear('updated_at', now()->year);
                break;
        }

        return $query->select('service_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(COALESCE(final_shop_revenue, shop_revenue)) as revenue'))
            ->with('service:id,service_name')
            ->groupBy('service_id')
            ->orderBy('order_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Generate downloadable PDF report.
     */
    public function downloadReport(Request $request)
    {
        $shop = Shop::where('owner_id', $request->user()->id)->first();

        if (!$shop) {
            return response()->json(['message' => 'No shop found for this owner'], 404);
        }

        $period = $request->query('period', 'daily');
        
        // Get period label and date range
        $periodInfo = $this->getPeriodInfo($period);
        
        // Get filtered orders
        $query = Order::where('shop_id', $shop->id)
            ->where('order_status', 'delivered')
            ->with(['service', 'customer']);

        switch ($period) {
            case 'daily':
                $query->whereDate('updated_at', today());
                break;
            case 'weekly':
                $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('updated_at', now()->month)
                      ->whereYear('updated_at', now()->year);
                break;
            case 'yearly':
                $query->whereYear('updated_at', now()->year);
                break;
        }

        $orders = $query->orderBy('updated_at', 'desc')->get();
        
        $totalRevenue = $orders->sum(function($order) {
            return $order->final_shop_revenue ?? $order->shop_revenue;
        });
        
        $data = [
            'shop' => $shop,
            'period' => $periodInfo['label'],
            'dateRange' => $periodInfo['range'],
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $orders->count(),
            'generatedAt' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('reports.shop-sales', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('shop-sales-report-' . $period . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Get period information for reports.
     */
    private function getPeriodInfo($period)
    {
        switch ($period) {
            case 'daily':
                return [
                    'label' => 'Daily Report',
                    'range' => today()->format('F d, Y'),
                ];
            case 'weekly':
                return [
                    'label' => 'Weekly Report',
                    'range' => now()->startOfWeek()->format('F d') . ' - ' . now()->endOfWeek()->format('F d, Y'),
                ];
            case 'monthly':
                return [
                    'label' => 'Monthly Report',
                    'range' => now()->format('F Y'),
                ];
            case 'yearly':
                return [
                    'label' => 'Yearly Report',
                    'range' => now()->format('Y'),
                ];
            default:
                return [
                    'label' => 'Sales Report',
                    'range' => 'All Time',
                ];
        }
    }

    /**
     * Get or create shop for owner.
     */
    public function getShop(Request $request)
    {
        $shop = Shop::where('owner_id', $request->user()->id)->first();
        return response()->json($shop);
    }

    /**
     * Create or update shop.
     */
    public function updateShop(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_permit' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'owner_id_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'proof_of_address' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $data = $request->only(['shop_name', 'description', 'address', 'contact_number']);

        // Handle photo upload - convert to base64
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $imageData = file_get_contents($file->getRealPath());
            $base64 = base64_encode($imageData);
            $mimeType = $file->getMimeType();
            $data['photo_path'] = 'data:' . $mimeType . ';base64,' . $base64;
        }

        // Handle verification documents
        if ($request->hasFile('business_permit')) {
            $data['business_permit_path'] = $request->file('business_permit')->store('shop_documents', 'public');
        }
        if ($request->hasFile('owner_id_document')) {
            $data['owner_id_path'] = $request->file('owner_id_document')->store('shop_documents', 'public');
        }
        if ($request->hasFile('proof_of_address')) {
            $data['proof_of_address_path'] = $request->file('proof_of_address')->store('shop_documents', 'public');
        }

        // If submitting verification documents, set status to pending
        if ($request->hasFile('business_permit') || $request->hasFile('owner_id_document') || $request->hasFile('proof_of_address')) {
            $data['verification_status'] = 'pending';
            $data['rejection_reason'] = null;
        }

        $shop = Shop::updateOrCreate(
            ['owner_id' => $request->user()->id],
            $data
        );

        return response()->json([
            'message' => 'Shop updated successfully',
            'shop' => $shop,
        ]);
    }

    /**
     * Get all services for the shop.
     */
    public function getServices(Request $request)
    {
        $shop = Shop::where('owner_id', $request->user()->id)->first();

        if (!$shop) {
            return response()->json([
                'message' => 'No shop found for this owner',
            ], 404);
        }

        $services = Service::where('shop_id', $shop->id)
            ->with('category')
            ->get();

        return response()->json($services);
    }

    /**
     * Get all categories.
     */
    public function getCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Create a new service.
     */
    public function createService(Request $request)
    {
        $shop = Shop::where('owner_id', $request->user()->id)->first();

        if (!$shop) {
            return response()->json([
                'message' => 'Please create a shop first',
            ], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $service = Service::create([
            'shop_id' => $shop->id,
            'category_id' => $request->category_id,
            'service_name' => $request->service_name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service->load('category'),
        ], 201);
    }

    /**
     * Update a service.
     */
    public function updateService(Request $request, $id)
    {
        $shop = Shop::where('owner_id', $request->user()->id)->first();
        $service = Service::where('id', $id)->where('shop_id', $shop->id)->first();

        if (!$service) {
            return response()->json([
                'message' => 'Service not found',
            ], 404);
        }

        $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'service_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $service->update($request->only(['category_id', 'service_name', 'description', 'price']));

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service->load('category'),
        ]);
    }

    /**
     * Delete a service.
     */
    public function deleteService(Request $request, $id)
    {
        $shop = Shop::where('owner_id', $request->user()->id)->first();
        $service = Service::where('id', $id)->where('shop_id', $shop->id)->first();

        if (!$service) {
            return response()->json([
                'message' => 'Service not found',
            ], 404);
        }

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully',
        ]);
    }

    /**
     * Get all orders for the shop.
     */
    public function getOrders(Request $request)
    {
        $shop = Shop::where('owner_id', $request->user()->id)->first();

        if (!$shop) {
            return response()->json([
                'message' => 'No shop found for this owner',
            ], 404);
        }

        $orders = Order::where('shop_id', $shop->id)
            ->with(['customer', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $shop = Shop::where('owner_id', $request->user()->id)->first();
        $order = Order::where('id', $id)->where('shop_id', $shop->id)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        $request->validate([
            'order_status' => 'required|in:pending,approved,processing,pending_payment,completed,ready_for_pickup,picked_up,for_delivery,delivered,cancelled',
            'weight_kg' => 'required_if:order_status,processing|nullable|numeric|min:0.1',
        ]);

        $updateData = ['order_status' => $request->order_status];

        // If marking as processing, calculate final amount based on weight
        if ($request->order_status === 'processing' && $request->weight_kg) {
            $pricePerKg = $order->service->price;
            $baseAmount = $pricePerKg * $request->weight_kg;
            
            // Add delivery fee to final amount
            $finalAmount = $baseAmount + $order->delivery_fee;
            $finalAdminRevenue = $baseAmount * 0.10;
            $finalShopRevenue = $baseAmount * 0.90;

            $updateData['weight_kg'] = $request->weight_kg;
            $updateData['final_amount'] = $finalAmount;
            $updateData['final_admin_revenue'] = $finalAdminRevenue;
            $updateData['final_shop_revenue'] = $finalShopRevenue;
            
            // If payment method is online, automatically move to pending_payment
            if ($order->payment_method === 'online') {
                $updateData['order_status'] = 'pending_payment';
            }
        }

        $order->update($updateData);

        // Create notification for customer
        $statusMessages = [
            'approved' => 'Your order has been approved',
            'processing' => $request->weight_kg 
                ? "Your laundry weighs {$request->weight_kg}kg. Total amount: ₱" . number_format($order->final_amount, 2) . ($order->delivery_fee > 0 ? " (includes ₱{$order->delivery_fee} delivery fee)" : "")
                : 'Your order is being processed',
            'pending_payment' => "Please complete your payment of ₱" . number_format($order->final_amount, 2) . ($order->delivery_fee > 0 ? " (includes ₱{$order->delivery_fee} delivery fee)" : ""),
            'completed' => 'Your order has been completed',
            'ready_for_pickup' => 'Your order is ready for pickup',
            'for_delivery' => 'Your order is out for delivery',
            'delivered' => 'Your order has been delivered',
            'picked_up' => 'Your order has been picked up',
        ];

        if (isset($statusMessages[$updateData['order_status']])) {
            Notification::create([
                'order_id' => $order->id,
                'user_id' => $order->customer_id,
                'message' => $statusMessages[$updateData['order_status']] . " (Order #{$order->tracking_number})",
            ]);
        }

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order->load(['customer', 'service']),
        ]);
    }

    /**
     * Cancel an order (pending or approved only).
     */
    public function cancelOrder(Request $request, $id)
    {
        $shop = Shop::where('owner_id', $request->user()->id)->first();
        $order = Order::where('id', $id)->where('shop_id', $shop->id)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        // Only allow canceling pending or approved orders
        if (!in_array($order->order_status, ['pending', 'approved'])) {
            return response()->json([
                'message' => 'Only pending or approved orders can be cancelled',
            ], 400);
        }

        $order->update(['order_status' => 'cancelled']);

        // Create notification for customer
        Notification::create([
            'order_id' => $order->id,
            'user_id' => $order->customer_id,
            'message' => "Your order has been cancelled by the shop (Order #{$order->tracking_number})",
        ]);

        return response()->json([
            'message' => 'Order cancelled successfully',
            'order' => $order->load(['customer', 'service']),
        ]);
    }

    /**
     * Update shop owner profile.
     */
    /**
     * Get shop owner profile.
     */
    public function getProfile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string',
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'phone', 'address']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Get shop owner notifications.
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
     * Update shop owner password.
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
