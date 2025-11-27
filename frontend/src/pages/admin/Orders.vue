<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">All Orders</h1>

    <!-- Search Bar same size but on right side -->
    <div class="mb-6 w-full max-w-md ml-auto">
      
      <div class="relative max-w-md">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by tracking number, customer, shop or service..."
          class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-spnr-blue-300 focus:border-transparent"
        />
        <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
    </div>

            <div v-else-if="filteredOrders.length > 0">
          <!-- Desktop table -->
          <div class="hidden sm:block card overflow-x-auto overflow-hidden border border-blue-300">
            <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shop</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in filteredOrders" :key="order.id">
            <td class="px-6 py-4 text-sm text-gray-900">{{ order.tracking_number }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ order.customer?.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ order.shop?.shop_name }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ order.service?.service_name }}</td>
            <td class="px-6 py-4 text-sm">
              <span :class="{
                'bg-yellow-100 text-yellow-800': order.order_status === 'pending',
                'bg-blue-100 text-blue-800': order.order_status === 'approved' || order.order_status === 'processing',
                'bg-purple-100 text-purple-800': order.order_status === 'for_delivery',
                'bg-orange-100 text-orange-800': order.order_status === 'ready_for_pickup',
                'bg-green-100 text-green-800': order.order_status === 'delivered' || order.order_status === 'picked_up',
                'bg-red-100 text-red-800': order.order_status === 'cancelled',
              }" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ order.order_status.replace(/_/g, ' ').toUpperCase() }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm font-semibold text-spnr-blue-900">₱{{ (order.final_amount || order.total_amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
          </tr>
        </tbody>
        </table>
      </div>

      <!-- Mobile cards -->
      <div class="sm:hidden space-y-4">
        <div v-for="order in filteredOrders" :key="order.id" class="card bg-gray-50 overflow-hidden border border-blue-300">
          <!-- Compact variant for mobile -->
          <div v-if="compactMobile" class="p-2 flex items-center justify-between">
            <div>
              <div class="text-sm font-semibold text-gray-800">{{ order.service?.service_name }}</div>
              <div class="text-xs text-gray-500">{{ order.tracking_number }} • {{ order.customer?.name }}</div>
            </div>
            <div class="text-right">
              <div class="text-sm font-semibold text-gray-700">₱{{ (order.final_amount || order.total_amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
              <div class="text-xs mt-1"><span :class="statusClass(order.order_status)" class="px-2 py-0.5 rounded-full text-xs">{{ order.order_status.replace(/_/g, ' ').toUpperCase() }}</span></div>
            </div>
          </div>
          <!-- Full variant -->
          <div v-else class="flex flex-col gap-4 p-4">
            <div>
              <h3 class="text-lg font-semibold text-gray-800">{{ order.service?.service_name }}</h3>
              <p class="text-sm text-gray-600">{{ order.shop?.shop_name }} • {{ order.customer?.name }}</p>
              <p class="text-xs text-gray-500 font-mono">{{ order.tracking_number }}</p>
            </div>
            <div class="text-right">
              <p class="text-sm text-gray-500 mb-1">Status: <span :class="statusClass(order.order_status)" class="px-2 py-0.5 rounded-full text-xs">{{ order.order_status.replace(/_/g, ' ').toUpperCase() }}</span></p>
              <p class="text-2xl font-bold text-spnr-blue-900">₱{{ (order.final_amount || order.total_amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="text-gray-600">{{ searchQuery ? 'No orders found matching your search' : 'No orders available' }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import api from '../../services/api'

const loading = ref(true)
const orders = ref([])
const searchQuery = ref('')
const compactMobile = ref(false)
const updateCompact = () => { compactMobile.value = window.innerWidth < 640 }

const filteredOrders = computed(() => {
  if (!searchQuery.value) {
    return orders.value
  }
  
  const query = searchQuery.value.toLowerCase()
  return orders.value.filter(order => {
    return (
      order.tracking_number?.toLowerCase().includes(query) ||
      order.customer?.name?.toLowerCase().includes(query) ||
      order.shop?.shop_name?.toLowerCase().includes(query) ||
      order.service?.service_name?.toLowerCase().includes(query) ||
      order.order_status?.toLowerCase().includes(query)
    )
  })
})

onMounted(async () => {
  const response = await api.get('/admin/orders')
  orders.value = response.data
  loading.value = false
  updateCompact()
  window.addEventListener('resize', updateCompact)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateCompact)
})

const statusClass = (status) => {
  const map = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'approved': 'bg-blue-100 text-blue-800',
    'processing': 'bg-blue-100 text-blue-800',
    'for_delivery': 'bg-purple-100 text-purple-800',
    'ready_for_pickup': 'bg-orange-100 text-orange-800',
    'delivered': 'bg-green-100 text-green-800',
    'picked_up': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return map[status] || 'bg-gray-100 text-gray-800'
}
</script>
