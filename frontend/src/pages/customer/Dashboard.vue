<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Customer Dashboard</h1>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
      <p class="mt-4 text-gray-600">Loading dashboard...</p>
    </div>

    <!-- Dashboard Content -->
    <div v-else>
      <!-- Stats Cards: responsive horizontal layout that wraps without horizontal scroll -->
      <div class="mb-8">
        <div class="flex flex-wrap items-stretch gap-4">
          <div class="stat-card flex flex-col justify-between border border-spnr-blue-300 bg-white p-3 min-w-[110px] md:min-w-[140px] flex-1">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600 mb-1">Active Orders</p>
                <h3 class="text-xl md:text-3xl font-bold text-spnr-blue-900">{{ stats.active_orders }}</h3>
              </div>
              <div class="w-10 h-10 md:w-12 md:h-12 bg-spnr-blue-900 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
              </div>
            </div>
          </div>

          <div class="stat-card flex flex-col justify-between border border-spnr-blue-300 bg-white p-3 min-w-[110px] md:min-w-[140px] flex-1">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600 mb-1">Completed Orders</p>
                <h3 class="text-xl md:text-3xl font-bold text-spnr-blue-900">{{ stats.completed_orders }}</h3>
              </div>
              <div class="w-10 h-10 md:w-12 md:h-12 bg-green-500 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
          </div>

          <div class="stat-card flex flex-col justify-between border border-spnr-blue-300 bg-white p-3 min-w-[110px] md:min-w-[140px] flex-1">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600 mb-1">Total Spent</p>
                <h3 class="text-xl md:text-3xl font-bold text-spnr-blue-900">₱{{ stats.total_spent.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</h3>
              </div>
              <div class="w-10 h-10 md:w-12 md:h-12 bg-spnr-blue-300 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card mb-8 border border-spnr-blue-300">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="flex flex-wrap items-stretch gap-3">
          <router-link
            to="/customer/shops"
            class="flex items-center justify-center p-3 bg-spnr-blue-50 hover:bg-spnr-blue-100 rounded-lg transition-colors flex-1 min-w-[120px]"
          >
            <svg class="w-4 h-4 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="text-sm font-medium text-spnr-blue-900">Book Service</span>
          </router-link>

          <router-link
            to="/customer/track"
            class="flex items-center justify-center p-3 bg-spnr-blue-50 hover:bg-spnr-blue-100 rounded-lg transition-colors flex-1 min-w-[120px]"
          >
            <svg class="w-4 h-4 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="text-sm font-medium text-spnr-blue-900">Track Order</span>
          </router-link>

          <router-link
            to="/customer/orders"
            class="flex items-center justify-center p-3 bg-spnr-blue-50 hover:bg-spnr-blue-100 rounded-lg transition-colors flex-1 min-w-[120px]"
          >
            <svg class="w-4 h-4 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="text-sm font-medium text-spnr-blue-900">My Orders</span>
          </router-link>

          <router-link
            to="/customer/profile"
            class="flex items-center justify-center p-3 bg-spnr-blue-50 hover:bg-spnr-blue-100 rounded-lg transition-colors flex-1 min-w-[120px]"
          >
            <svg class="w-4 h-4 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-sm font-medium text-spnr-blue-900">Profile</span>
          </router-link>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="card border border-spnr-blue-100" v-if="recentOrders.length > 0" >
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold text-gray-800">Recent Orders</h2>
          <router-link to="/customer/orders" class="text-sm text-spnr-blue-900 hover:text-spnr-blue-700 font-medium">
            View All
          </router-link>
        </div>
        <div class="space-y-4">
          <div
            v-for="order in recentOrders"
            :key="order.id"
            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
          >
            <div>
              <p class="font-medium text-gray-800">{{ order.service?.service_name }}</p>
              <p class="text-sm text-gray-600">{{ order.shop?.shop_name }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ order.tracking_number }}</p>
            </div>
            <div class="text-right">
              <span
                :class="{
                  'bg-yellow-100 text-yellow-800': order.order_status === 'pending',
                  'bg-blue-100 text-blue-800': order.order_status === 'approved' || order.order_status === 'processing',
                  'bg-indigo-100 text-indigo-800': order.order_status === 'completed',
                  'bg-teal-100 text-teal-800': order.order_status === 'ready_for_pickup',
                  'bg-orange-100 text-orange-800': order.order_status === 'for_delivery',
                  'bg-green-100 text-green-800': order.order_status === 'delivered' || order.order_status === 'picked_up',
                }"
                class="px-3 py-1 rounded-full text-xs font-medium"
              >
                {{ formatStatus(order.order_status) }}
              </span>
              <p v-if="order.weight_kg" class="text-xs text-gray-600 mt-1">{{ order.weight_kg }}kg</p>
              <p class="text-sm font-semibold text-gray-800 mt-2">₱{{ order.final_amount || order.total_amount }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const loading = ref(true)
const stats = ref({
  active_orders: 0,
  completed_orders: 0,
  total_spent: 0,
})
const recentOrders = ref([])

const fetchDashboard = async () => {
  try {
    const [statsRes, ordersRes] = await Promise.all([
      api.get('/customer/dashboard'),
      api.get('/customer/orders'),
    ])
    
    stats.value = statsRes.data
    recentOrders.value = ordersRes.data.slice(0, 3)
  } catch (error) {
    console.error('Error fetching dashboard:', error)
  } finally {
    loading.value = false
  }
}

const formatStatus = (status) => {
  return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

onMounted(() => {
  fetchDashboard()
})
</script>
