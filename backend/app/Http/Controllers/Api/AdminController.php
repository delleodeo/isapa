<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    /**
     * Get admin dashboard stats.
     */
    public function getDashboard()
    {
        // Get period parameter (default to 'all')
        $period = request()->query('period', 'all');
        
        // Base query for completed orders (delivered or picked_up)
        $baseQuery = Order::whereIn('order_status', ['delivered', 'picked_up']);

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
            'total_customers' => User::where('role', 'customer')->count(),
            'total_owners' => User::where('role', 'owner')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'total_revenue' => $baseQuery->sum(DB::raw('COALESCE(final_admin_revenue, admin_revenue)')),
            'period_revenue' => $query->sum(DB::raw('COALESCE(final_admin_revenue, admin_revenue)')),
            'period_orders' => $query->count(),
            
            'shop_revenues' => $baseQuery
                ->selectRaw('shop_id, SUM(COALESCE(final_admin_revenue, admin_revenue)) as admin_earnings, SUM(COALESCE(final_shop_revenue, shop_revenue)) as shop_earnings, SUM(COALESCE(final_amount, total_amount)) as total')
                ->with('shop:id,shop_name')
                ->groupBy('shop_id')
                ->get(),
            
            // Daily sales - filtered by period
            'daily_sales' => $this->getDailySales($period),
            
            // Monthly sales - filtered by period
            'monthly_sales' => $this->getMonthlySales($period),
            
            // Top performing shops - filtered by period
            'top_shops' => $this->getTopShops($period),
        ];

        return response()->json($stats);
    }

    /**
     * Get daily sales filtered by period.
     */
    private function getDailySales($period)
    {
        $query = Order::whereIn('order_status', ['delivered', 'picked_up']);

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

        return $query->selectRaw('DATE(updated_at) as date, SUM(COALESCE(final_admin_revenue, admin_revenue)) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Get monthly sales filtered by period.
     */
    private function getMonthlySales($period)
    {
        $query = Order::whereIn('order_status', ['delivered', 'picked_up']);

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

        return $query->selectRaw('MONTH(updated_at) as month, SUM(COALESCE(final_admin_revenue, admin_revenue)) as revenue, COUNT(*) as orders')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    /**
     * Get top shops filtered by period.
     */
    private function getTopShops($period)
    {
        $query = Order::whereIn('order_status', ['delivered', 'picked_up']);

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

        return $query->select('shop_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(COALESCE(final_admin_revenue, admin_revenue)) as admin_revenue'))
            ->with('shop:id,shop_name')
            ->groupBy('shop_id')
            ->orderBy('admin_revenue', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Generate downloadable PDF report.
     */
    public function downloadReport(Request $request)
    {
        $period = $request->query('period', 'daily');
        
        // Get period label and date range
        $periodInfo = $this->getPeriodInfo($period);
        
        // Get filtered orders
        $query = Order::where('order_status', 'delivered')
            ->with(['service', 'customer', 'shop']);

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
        
        // Calculate totals
        $totalAdminRevenue = $orders->sum(function($order) {
            return $order->final_admin_revenue ?? $order->admin_revenue;
        });
        
        $totalShopRevenue = $orders->sum(function($order) {
            return $order->final_shop_revenue ?? $order->shop_revenue;
        });
        
        // Group by shop
        $shopBreakdown = $orders->groupBy('shop_id')->map(function($shopOrders) {
            $shop = $shopOrders->first()->shop;
            $adminRevenue = $shopOrders->sum(function($order) {
                return $order->final_admin_revenue ?? $order->admin_revenue;
            });
            $shopRevenue = $shopOrders->sum(function($order) {
                return $order->final_shop_revenue ?? $order->shop_revenue;
            });
            
            return [
                'shop_name' => $shop->shop_name,
                'orders_count' => $shopOrders->count(),
                'admin_revenue' => $adminRevenue,
                'shop_revenue' => $shopRevenue,
                'total_revenue' => $adminRevenue + $shopRevenue,
            ];
        })->sortByDesc('admin_revenue')->values();
        
        $data = [
            'period' => $periodInfo['label'],
            'dateRange' => $periodInfo['range'],
            'totalAdminRevenue' => $totalAdminRevenue,
            'totalShopRevenue' => $totalShopRevenue,
            'totalRevenue' => $totalAdminRevenue + $totalShopRevenue,
            'totalOrders' => $orders->count(),
            'shopBreakdown' => $shopBreakdown,
            'generatedAt' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('reports.admin-revenue', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('admin-revenue-report-' . $period . '-' . now()->format('Y-m-d') . '.pdf');
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
                    'label' => 'Revenue Report',
                    'range' => 'All Time',
                ];
        }
    }

    /**
     * Get all users.
     */
    public function getUsers(Request $request)
    {
        $role = $request->query('role');
        
        $query = User::query();
        
        if ($role && in_array($role, ['customer', 'owner'])) {
            $query->where('role', $role);
        }

        $users = $query->with('shop')->orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }

    /**
     * Toggle user active status.
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Cannot modify admin status',
            ], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Get all categories.
     */
    public function getCategories()
    {
        $categories = Category::withCount('services')->get();
        return response()->json($categories);
    }

    /**
     * Create a new category.
     */
    public function createCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->only(['name', 'description']));

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    /**
     * Update a category.
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->only(['name', 'description']));

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    }

    /**
     * Delete a category.
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);

        if ($category->services()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with associated services',
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * Get all orders.
     */
    public function getOrders()
    {
        $orders = Order::with(['customer', 'shop', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Update admin email.
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
            'password' => 'required',
        ]);

        $user = $request->user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password is incorrect',
            ], 400);
        }

        // Update email
        $user->update([
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Email updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Update admin password.
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

    /**
     * Get pending shop verifications.
     */
    public function getPendingShops()
    {
        $shops = Shop::where('verification_status', 'pending')
            ->with('owner')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($shops);
    }

    /**
     * Get all shops with verification status.
     */
    public function getShops(Request $request)
    {
        $status = $request->query('status');
        
        $query = Shop::with('owner');
        
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('verification_status', $status);
        }

        $shops = $query->orderBy('created_at', 'desc')->get();

        return response()->json($shops);
    }

    /**
     * Approve shop verification.
     */
    public function approveShop($id)
    {
        $shop = Shop::with('owner')->findOrFail($id);

        if ($shop->verification_status === 'approved') {
            return response()->json([
                'message' => 'Shop is already approved',
            ], 400);
        }

        $shop->update([
            'verification_status' => 'approved',
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        return response()->json([
            'message' => 'Shop approved successfully',
            'shop' => $shop,
        ]);
    }

    /**
     * Reject shop verification.
     */
    public function rejectShop(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $shop = Shop::with('owner')->findOrFail($id);

        if ($shop->verification_status === 'rejected') {
            return response()->json([
                'message' => 'Shop is already rejected',
            ], 400);
        }

        $shop->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->reason,
            'verified_at' => null,
        ]);

        return response()->json([
            'message' => 'Shop verification rejected',
            'shop' => $shop,
        ]);
    }
}
