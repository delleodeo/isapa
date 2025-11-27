<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-8">My Orders</h1>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
      <p class="mt-4 text-gray-600">Loading orders...</p>
    </div>

    <div v-else>
      <!-- Active Orders Section -->
      <div class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Active Orders</h2>
        
        <div v-if="activeOrders.length > 0" class="space-y-4">
          <div
            v-for="order in activeOrders"
            :key="order.id"
            class="card overflow-hidden border border-blue-300"
          >
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="text-lg font-semibold text-gray-800">{{ order.service?.service_name }}</h3>
              <span
                :class="{
                  'bg-yellow-100 text-yellow-800': order.order_status === 'pending',
                  'bg-blue-100 text-blue-800': order.order_status === 'approved',
                  'bg-purple-100 text-purple-800': order.order_status === 'processing',
                  'bg-rose-100 text-rose-800': order.order_status === 'pending_payment',
                  'bg-indigo-100 text-indigo-800': order.order_status === 'completed',
                  'bg-teal-100 text-teal-800': order.order_status === 'ready_for_pickup',
                  'bg-orange-100 text-orange-800': order.order_status === 'for_delivery',
                  'bg-green-100 text-green-800': order.order_status === 'delivered' || order.order_status === 'picked_up',
                  'bg-red-100 text-red-800': order.order_status === 'cancelled',
                }"
                class="px-3 py-1 rounded-full text-xs font-medium"
              >
                {{ formatStatus(order.order_status) }}
              </span>
            </div>
            
            <div class="space-y-1 text-sm text-gray-600">
              <p class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ order.shop?.shop_name }}
              </p>
              <p class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
                Tracking: {{ order.tracking_number }}
              </p>
              <p class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ formatDate(order.created_at) }}
              </p>
            </div>
          </div>

          <div class="text-right">
            <p v-if="order.weight_kg" class="text-sm text-gray-600 mb-1">Weight: {{ order.weight_kg }}kg</p>
            <p class="text-2xl font-bold text-spnr-blue-900">₱{{ order.final_amount || order.total_amount }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ order.contact_number }}</p>
            
            <!-- Pay Now button for online payment when pending_payment or processing with weight -->
            <button
              v-if="order.payment_method === 'online' && (order.order_status === 'pending_payment' || (order.order_status === 'processing' && order.weight_kg)) && !order.paid_at"
              @click="payNow(order)"
              class="mt-3 px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200 w-full flex items-center justify-center shadow-md"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
              </svg>
              Pay Now - ₱{{ order.final_amount }}
            </button>
            
            <div v-if="order.paid_at" class="mt-3 flex items-center justify-center text-green-600 text-sm font-medium">
              <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              Paid
            </div>
            
            <!-- Cancel button for pending orders -->
            <button
              v-if="order.order_status === 'pending'"
              @click="cancelOrder(order)"
              class="mt-3 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200"
            >
              Cancel Order
            </button>
          </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
          <template v-if="order.delivery_type === 'delivery'">
            <p class="text-sm font-semibold text-spnr-blue-900 mb-1">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span class="font-semibold">Pickup Address:</span> <span class="font-normal text-gray-700">{{ order.pickup_address }}</span>
            </p>
            <p class="text-sm font-semibold text-spnr-blue-900">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
              <span class="font-semibold">Delivery Address:</span> <span class="font-normal text-gray-700">{{ order.delivery_address }}</span>
            </p>
          </template>
          <template v-else>
            <p class="text-sm font-semibold text-blue-400">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
              Drop-off / Pick-up at Shop
            </p>
          </template>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="text-gray-600">No active orders</p>
    </div>
  </div>

  <!-- Order History Section -->
  <div>
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-semibold text-gray-800">Order History</h2>

      <div class="flex items-center gap-2">
        <!-- Search Bar for Order History (only if > 10 orders) -->
        <div v-if="completedOrders.length > 10" class="relative max-w-md">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search orders..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-spnr-blue-300"
          />
          <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>

        <!-- Compact mode auto-applies on mobile (no toggle) -->
      </div>
    </div>
    
    <!-- Table view for more than 10 orders (desktop only) -->
    <div v-if="completedOrders.length > 10 && filteredCompletedOrders.length > 0" class="hidden sm:block">
      <div class="card overflow-x-auto overflow-hidden border border-blue-300">
        <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shop</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in filteredCompletedOrders" :key="order.id">
            <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ order.tracking_number }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ order.shop?.shop_name }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ order.service?.service_name }}</td>
            <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(order.created_at) }}</td>
            <td class="px-6 py-4 text-sm">
              <span :class="{
                'bg-green-100 text-green-800': order.order_status === 'delivered' || order.order_status === 'picked_up',
                'bg-red-100 text-red-800': order.order_status === 'cancelled'
              }" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ formatStatus(order.order_status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm font-semibold text-spnr-blue-900">₱{{ order.final_amount || order.total_amount }}</td>
          </tr>
        </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile card list for large histories (mobile friendly) -->
    <div v-if="completedOrders.length > 10 && filteredCompletedOrders.length > 0" class="sm:hidden space-y-4">
      <div v-for="order in filteredCompletedOrders" :key="order.id" class="card bg-gray-50 overflow-hidden border border-blue-300">
        <!-- Compact variant for mobile -->
        <div v-if="compactMobile" class="p-2 flex items-center justify-between">
          <div>
            <div class="text-sm font-semibold text-gray-800">{{ order.service?.service_name }}</div>
            <div class="text-xs text-gray-500">{{ order.tracking_number }} • {{ order.shop?.shop_name }}</div>
          </div>
          <div class="text-right">
            <div class="text-sm font-semibold text-gray-700">₱{{ order.final_amount || order.total_amount }}</div>
            <div class="text-xs mt-1"><span :class="{'bg-green-100 text-green-800': order.order_status === 'delivered' || order.order_status === 'picked_up', 'bg-red-100 text-red-800': order.order_status === 'cancelled'}" class="px-2 py-0.5 rounded-full text-xs">{{ formatStatus(order.order_status) }}</span></div>
          </div>
        </div>
        <!-- Full variant -->
        <div v-else class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-800">{{ order.service?.service_name }}</h3>
            <p class="text-sm text-gray-600">{{ order.shop?.shop_name }}</p>
            <p class="text-xs text-gray-500 font-mono">{{ order.tracking_number }}</p>
            <p class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
            <span :class="{
              'bg-green-100 text-green-800': order.order_status === 'delivered' || order.order_status === 'picked_up',
              'bg-red-100 text-red-800': order.order_status === 'cancelled'
            }" class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium">
              {{ formatStatus(order.order_status) }}
            </span>
          </div>
          <div class="text-right">
            <p v-if="order.weight_kg" class="text-sm text-gray-500 mb-1">{{ order.weight_kg }}kg</p>
            <p class="text-2xl font-bold text-gray-700">₱{{ order.final_amount || order.total_amount }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Card view for 10 or fewer orders -->
    <div v-else-if="completedOrders.length > 0 && completedOrders.length <= 10" class="space-y-4">
      <div v-for="order in completedOrders" :key="order.id" class="card bg-gray-50 overflow-hidden border border-blue-300">
        <div v-if="compactMobile" class="p-2 flex items-center justify-between">
          <div>
            <div class="text-sm font-semibold text-gray-800">{{ order.service?.service_name }}</div>
            <div class="text-xs text-gray-500">{{ order.tracking_number }} • {{ order.shop?.shop_name }}</div>
          </div>
          <div class="text-right">
            <div class="text-sm font-semibold text-gray-700">₱{{ order.final_amount || order.total_amount }}</div>
            <div class="text-xs mt-1"><span :class="{'bg-green-100 text-green-800': order.order_status === 'delivered' || order.order_status === 'picked_up', 'bg-red-100 text-red-800': order.order_status === 'cancelled'}" class="px-2 py-0.5 rounded-full text-xs">{{ formatStatus(order.order_status) }}</span></div>
          </div>
        </div>
        <div v-else class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-800">{{ order.service?.service_name }}</h3>
            <p class="text-sm text-gray-600">{{ order.shop?.shop_name }}</p>
            <p class="text-xs text-gray-500 font-mono">{{ order.tracking_number }}</p>
            <p class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
            <span :class="{
              'bg-green-100 text-green-800': order.order_status === 'delivered' || order.order_status === 'picked_up',
              'bg-red-100 text-red-800': order.order_status === 'cancelled'
            }" class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium">
              {{ formatStatus(order.order_status) }}
            </span>
          </div>
          <div class="text-right">
            <p v-if="order.weight_kg" class="text-sm text-gray-500 mb-1">{{ order.weight_kg }}kg</p>
            <p class="text-2xl font-bold text-gray-700">₱{{ order.final_amount || order.total_amount }}</p>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
      </svg>
      <p class="text-gray-600">{{ searchQuery ? 'No orders found matching your search' : 'No order history yet' }}</p>
    </div>
  </div>
</div>

<!-- Empty State for no orders at all -->
<div v-if="!loading && orders.length === 0" class="text-center py-12 bg-gray-50 rounded-lg">
  <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
  </svg>
  <p class="text-gray-600 mb-4">You haven't placed any orders yet</p>
  <router-link to="/customer/shops" class="btn-primary">
    Browse Shops
  </router-link>
</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const loading = ref(true)
const orders = ref([])
const searchQuery = ref('')
const compactMobile = ref(false)
const updateCompact = () => {
  compactMobile.value = window.innerWidth < 640
}

const activeOrders = computed(() => {
  return orders.value.filter(order => {
    return !['delivered', 'picked_up', 'cancelled'].includes(order.order_status)
  })
})

const completedOrders = computed(() => {
  return orders.value.filter(order => {
    return ['delivered', 'picked_up', 'cancelled'].includes(order.order_status)
  })
})

const filteredCompletedOrders = computed(() => {
  if (!searchQuery.value) {
    return completedOrders.value
  }
  
  const query = searchQuery.value.toLowerCase()
  return completedOrders.value.filter(order => {
    return (
      order.tracking_number?.toLowerCase().includes(query) ||
      order.shop?.shop_name?.toLowerCase().includes(query) ||
      order.service?.service_name?.toLowerCase().includes(query) ||
      order.order_status?.toLowerCase().includes(query)
    )
  })
})

const fetchOrders = async () => {
  try {
    const response = await api.get('/customer/orders')
    orders.value = response.data
  } catch (error) {
    console.error('Error fetching orders:', error)
  } finally {
    loading.value = false
  }
}

const formatStatus = (status) => {
  return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const payNow = (order) => {
  router.push(`/customer/payment/${order.id}`)
}

const cancelOrder = async (order) => {
  if (!confirm(`Are you sure you want to cancel order #${order.tracking_number}?`)) {
    return
  }

  try {
    await api.delete(`/customer/orders/${order.id}`)
    
    // Remove order from the list
    orders.value = orders.value.filter(o => o.id !== order.id)
    
    alert('Order cancelled successfully')
  } catch (error) {
    console.error('Error cancelling order:', error)
    alert(error.response?.data?.message || 'Failed to cancel order')
  }
}

onMounted(() => {
  fetchOrders()
  updateCompact()
  window.addEventListener('resize', updateCompact)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateCompact)
})
</script>
