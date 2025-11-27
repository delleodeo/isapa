<template>
  <div>
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8">Manage Orders</h1>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
    </div>

    <div v-else>
      <!-- Active Orders Section -->
      <div class="mb-8">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">Active Orders</h2>
        
        <div v-if="activeOrders.length > 0" class="space-y-4">
          <div v-for="order in activeOrders" :key="order.id" class="card overflow-hidden border border-blue-300">
            <!-- Desktop View -->
            <div class="hidden sm:flex sm:flex-row sm:justify-between sm:items-start gap-4">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800">{{ order.service?.service_name }}</h3>
                <p class="text-sm text-gray-600">Customer: {{ order.customer?.name }}</p>
                <p class="text-xs text-gray-500 font-mono">{{ order.tracking_number }}</p>
                <p class="text-sm text-gray-600 mt-2">{{ order.contact_number }}</p>
                <p v-if="order.delivery_type === 'delivery'" class="text-sm font-semibold text-spnr-blue-900">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span class="font-semibold">Pickup:</span> {{ order.pickup_address }}
                </p>
                <p v-if="order.delivery_type === 'delivery'" class="text-sm font-semibold text-spnr-blue-900">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                  </svg>
                  <span class="font-semibold">Delivery:</span> {{ order.delivery_address }}
                </p>
                <p v-if="order.delivery_type === 'pickup'" class="text-sm font-semibold text-blue-400">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                  Drop-off / Pick-up at Shop
                </p>
                <div class="mt-2 flex flex-wrap gap-2 items-center">
                  <span class="inline-block px-3 py-1 rounded-full text-xs font-medium" :class="getStatusClass(order.order_status)">
                    {{ formatStatus(order.order_status) }}
                  </span>
                  <!-- Payment Status Indicator - only show when paid (not pending_payment status) -->
                  <span v-if="order.payment_method === 'online' && order.paid_at" 
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Paid
                  </span>
                </div>
              </div>
              <div class="text-right">
                <!-- Cancel Button - Top of Right Column -->
                <button
                  v-if="order.order_status === 'pending' || order.order_status === 'approved'"
                  @click="cancelOrder(order)"
                  class="inline-block mb-2 text-xs px-2.5 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors"
                >
                  Cancel Order
                </button>
                <p class="text-sm text-gray-600 mb-1">Price per kg: ₱{{ order.service?.price }}</p>
                <p v-if="order.weight_kg" class="text-sm font-semibold text-gray-700">Weight: {{ order.weight_kg }}kg</p>
                <p class="text-sm text-gray-600">Payment: <span class="font-medium">{{ order.payment_method === 'cash' ? (order.delivery_type === 'pickup' ? 'Pay Over Counter' : 'Cash on Delivery') : 'Online' }}</span></p>
                <p v-if="order.final_amount" class="text-2xl font-bold text-spnr-blue-900">₱{{ order.final_amount }}</p>
                <p v-else class="text-2xl font-bold text-gray-500">₱{{ order.total_amount }}/kg</p>
                <select
                  :value="order.order_status"
                  @change="updateStatus(order, $event.target.value)"
                  class="mt-2 text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-spnr-blue-300"
                >
                  <option value="pending" :disabled="isStatusDisabled(order.order_status, 'pending')">Pending</option>
                  <option value="approved" :disabled="isStatusDisabled(order.order_status, 'approved')">Approved</option>
                  <option value="processing" :disabled="isStatusDisabled(order.order_status, 'processing')">Processing</option>
                  <!-- Show different workflow based on delivery type -->
                  <template v-if="order.delivery_type === 'pickup'">
                    <option value="completed" :disabled="isStatusDisabled(order.order_status, 'completed') || isPaymentPending(order)">Completed</option>
                    <option value="ready_for_pickup" :disabled="isStatusDisabled(order.order_status, 'ready_for_pickup') || isPaymentPending(order)">Ready for Pick-up</option>
                    <option value="picked_up" :disabled="isStatusDisabled(order.order_status, 'picked_up')|| isPaymentPending(order)">Picked Up</option>
                  </template>
                  <template v-else>
                    <option value="completed" :disabled="isStatusDisabled(order.order_status, 'completed') || isPaymentPending(order)">Completed</option>
                    <option value="for_delivery" :disabled="isStatusDisabled(order.order_status, 'for_delivery') || isPaymentPending(order)">For Delivery</option>
                    <option value="delivered" :disabled="isStatusDisabled(order.order_status, 'delivered') || isPaymentPending(order)">Delivered</option>
                  </template>
                </select>
              </div>
            </div>

            <!-- Mobile View -->
            <div class="sm:hidden">
              <div class="flex justify-between items-start gap-4 mb-3">
                <div class="flex-1">
                  <h3 class="text-base font-semibold text-gray-800">{{ order.service?.service_name }}</h3>
                  <p class="text-xs text-gray-500 font-mono">{{ order.tracking_number }}</p>
                </div>
                <!-- Cancel Button - Top Right for Mobile -->
                <button
                  v-if="order.order_status === 'pending' || order.order_status === 'approved'"
                  @click="cancelOrder(order)"
                  class="text-xs px-2.5 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors whitespace-nowrap"
                >
                  Cancel
                </button>
              </div>

              <div class="space-y-2 text-sm">
                <p class="text-gray-600">Customer: {{ order.customer?.name }}</p>
                <p class="text-gray-600">{{ order.contact_number }}</p>
                
                <p v-if="order.delivery_type === 'delivery'" class="font-semibold text-spnr-blue-900 text-xs">
                  <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span class="font-semibold">Pickup:</span> {{ order.pickup_address }}
                </p>
                <p v-if="order.delivery_type === 'delivery'" class="font-semibold text-spnr-blue-900 text-xs">
                  <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                  </svg>
                  <span class="font-semibold">Delivery:</span> {{ order.delivery_address }}
                </p>
                <p v-if="order.delivery_type === 'pickup'" class="font-semibold text-blue-400 text-xs">
                  <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                  Drop-off / Pick-up at Shop
                </p>

                <div class="flex flex-wrap gap-2 items-center">
                  <span class="inline-block px-2.5 py-1 rounded-full text-xs font-medium" :class="getStatusClass(order.order_status)">
                    {{ formatStatus(order.order_status) }}
                  </span>
                  <span v-if="order.payment_method === 'online' && order.paid_at" 
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Paid
                  </span>
                </div>

                <div class="pt-2 border-t border-gray-200">
                  <div class="flex justify-between items-center mb-1">
                    <span class="text-gray-600 text-xs">Price per kg:</span>
                    <span class="font-medium">₱{{ order.service?.price }}</span>
                  </div>
                  <div v-if="order.weight_kg" class="flex justify-between items-center mb-1">
                    <span class="text-gray-600 text-xs">Weight:</span>
                    <span class="font-semibold text-gray-700">{{ order.weight_kg }}kg</span>
                  </div>
                  <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 text-xs">Payment:</span>
                    <span class="font-medium">{{ order.payment_method === 'cash' ? (order.delivery_type === 'pickup' ? 'Pay Over Counter' : 'Cash on Delivery') : 'Online' }}</span>
                  </div>
                  <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-900 font-semibold">Total:</span>
                    <span v-if="order.final_amount" class="text-xl font-bold text-spnr-blue-900">₱{{ order.final_amount }}</span>
                    <span v-else class="text-xl font-bold text-gray-500">₱{{ order.total_amount }}/kg</span>
                  </div>
                  
                  <select
                    :value="order.order_status"
                    @change="updateStatus(order, $event.target.value)"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-spnr-blue-300"
                  >
                    <option value="pending" :disabled="isStatusDisabled(order.order_status, 'pending')">Pending</option>
                    <option value="approved" :disabled="isStatusDisabled(order.order_status, 'approved')">Approved</option>
                    <option value="processing" :disabled="isStatusDisabled(order.order_status, 'processing')">Processing</option>
                    <template v-if="order.delivery_type === 'pickup'">
                      <option value="completed" :disabled="isStatusDisabled(order.order_status, 'completed') || isPaymentPending(order)">Completed</option>
                      <option value="ready_for_pickup" :disabled="isStatusDisabled(order.order_status, 'ready_for_pickup') || isPaymentPending(order)">Ready for Pick-up</option>
                      <option value="picked_up" :disabled="isStatusDisabled(order.order_status, 'picked_up')|| isPaymentPending(order)">Picked Up</option>
                    </template>
                    <template v-else>
                      <option value="completed" :disabled="isStatusDisabled(order.order_status, 'completed') || isPaymentPending(order)">Completed</option>
                      <option value="for_delivery" :disabled="isStatusDisabled(order.order_status, 'for_delivery') || isPaymentPending(order)">For Delivery</option>
                      <option value="delivered" :disabled="isStatusDisabled(order.order_status, 'delivered') || isPaymentPending(order)">Delivered</option>
                    </template>
                  </select>
                </div>
              </div>
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
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-800">Order History</h2>

            <!-- Search Bar for Order History (only if > 10 orders) -->
            <div v-if="deliveredOrders.length > 10" class="relative max-w-md">
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
          </div>
        
        <!-- Table view for more than 10 orders (desktop only) -->
        <div v-if="deliveredOrders.length > 10 && filteredDeliveredOrders.length > 0" class="hidden sm:block">
          <div class="card overflow-x-auto overflow-hidden border border-blue-300">
            <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="order in filteredDeliveredOrders" :key="order.id">
                <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ order.tracking_number }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ order.customer?.name }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ order.service?.service_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(order.created_at) }}</td>
                <td class="px-6 py-4 text-sm">
                  <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ order.delivery_type === 'pickup' ? 'Picked Up' : 'Delivered' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm font-semibold text-spnr-blue-900">₱{{ order.final_amount || order.total_amount }}</td>
              </tr>
            </tbody>
            </table>
          </div>
        </div>

        <!-- Mobile card list for large histories (mobile friendly) -->
        <div v-if="deliveredOrders.length > 10 && filteredDeliveredOrders.length > 0" class="sm:hidden space-y-4">
          <div v-for="order in filteredDeliveredOrders" :key="order.id" class="card bg-gray-50 overflow-hidden border border-blue-300">
            <!-- Always compact on mobile -->
            <div class="p-2 flex items-center justify-between">
              <div>
                <div class="text-sm font-semibold text-gray-800">{{ order.service?.service_name }}</div>
                <div class="text-xs text-gray-500">{{ order.tracking_number }} • {{ order.customer?.name }}</div>
              </div>
              <div class="text-right">
                <div class="text-sm font-semibold text-gray-700">₱{{ order.final_amount || order.total_amount }}</div>
                <div class="text-xs mt-1"><span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ order.delivery_type === 'pickup' ? 'Picked Up' : 'Delivered' }}</span></div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Card view for 10 or fewer orders -->
        <div v-else-if="deliveredOrders.length > 0 && deliveredOrders.length <= 10" class="space-y-4">
          <div v-for="order in deliveredOrders" :key="order.id" class="card bg-gray-50 overflow-hidden border border-blue-300">
            <!-- Always compact on mobile -->
            <div class="sm:hidden p-2 flex items-center justify-between">
              <div>
                <div class="text-sm font-semibold text-gray-800">{{ order.service?.service_name }}</div>
                <div class="text-xs text-gray-500">{{ order.tracking_number }} • {{ order.customer?.name }}</div>
              </div>
              <div class="text-right">
                <div class="text-sm font-semibold text-gray-700">₱{{ order.final_amount || order.total_amount }}</div>
                <div class="text-xs mt-1"><span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ order.delivery_type === 'pickup' ? 'Picked Up' : 'Delivered' }}</span></div>
              </div>
            </div>
            <!-- Desktop view -->
            <div class="hidden sm:flex sm:flex-row sm:justify-between sm:items-start gap-4">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800">{{ order.service?.service_name }}</h3>
                <p class="text-sm text-gray-600">Customer: {{ order.customer?.name }}</p>
                <p class="text-xs text-gray-500 font-mono">{{ order.tracking_number }}</p>
                <p class="text-sm text-gray-600 mt-2">{{ order.contact_number }}</p>
                <p v-if="order.delivery_type === 'delivery'" class="text-sm font-semibold text-spnr-blue-900">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span class="font-semibold">Pickup:</span> {{ order.pickup_address }}
                </p>
                <p v-if="order.delivery_type === 'delivery'" class="text-sm font-semibold text-spnr-blue-900">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                  </svg>
                  <span class="font-semibold">Delivery:</span> {{ order.delivery_address }}
                </p>
                <p v-if="order.delivery_type === 'pickup'" class="text-sm font-semibold text-blue-500">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                  Drop-off / Pick-up at Shop
                </p>
                <div class="mt-2">
                  <span v-if="order.delivery_type === 'pickup'" class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Picked Up
                  </span>
                  <span v-else class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Delivered
                  </span>
                </div>
              </div>
              <div class="text-right">
                <p v-if="order.weight_kg" class="text-sm text-gray-500 mb-1">Weight: {{ order.weight_kg }}kg</p>
                <p v-if="order.final_amount" class="text-2xl font-bold text-gray-700">₱{{ order.final_amount }}</p>
                <p v-else class="text-2xl font-bold text-gray-700">₱{{ order.total_amount }}</p>
                <p class="text-xs text-gray-500 mt-2">Completed</p>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
          <p class="text-gray-600">{{ searchQuery ? 'No orders found matching your search' : 'No order history yet' }}</p>
        </div>
      </div>
    </div>

    <!-- Weight Input Modal -->
    <div v-if="showWeightModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeWeightModal">
      <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Set Laundry Weight</h2>
        <p class="text-sm text-gray-600 mb-4">Order: {{ selectedOrder?.tracking_number }}</p>
        <p class="text-sm text-gray-600 mb-4">Service: {{ selectedOrder?.service?.service_name }}</p>
        <p class="text-sm font-semibold text-spnr-blue-900 mb-4">Price: ₱{{ selectedOrder?.service?.price }}/kg</p>
        
        <form @submit.prevent="submitWeight" class="space-y-4">
          <div>
            <label class="label">Weight (kg) <span class="text-red-500">*</span></label>
            <input 
              v-model="weightInput" 
              type="number" 
              step="0.01" 
              min="0.1"
              required 
              class="input-field" 
              placeholder="Enter weight in kilograms"
              autofocus
            />
          </div>
          
          <div v-if="weightInput && selectedOrder" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Total Amount:</p>
            <p class="text-3xl font-bold text-spnr-blue-900">₱{{ calculateTotal }}</p>
          </div>

          <div class="flex gap-3">
            <button type="button" @click="closeWeightModal" class="flex-1 btn-outline">Cancel</button>
            <button type="submit" class="flex-1 btn-primary">Confirm & Process</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import api from '../../services/api'

const loading = ref(true)
const orders = ref([])
const showWeightModal = ref(false)
const selectedOrder = ref(null)
const weightInput = ref('')
const searchQuery = ref('')

const calculateTotal = computed(() => {
  if (!weightInput.value || !selectedOrder.value) return 0
  return (parseFloat(weightInput.value) * parseFloat(selectedOrder.value.service.price)).toFixed(2)
})

const activeOrders = computed(() => {
  return orders.value.filter(order => {
    // Exclude cancelled orders from active orders
    if (order.order_status === 'cancelled') {
      return false
    }
    // Pickup orders are completed when status is 'picked_up'
    if (order.delivery_type === 'pickup') {
      return order.order_status !== 'picked_up'
    }
    // Delivery orders are completed when status is 'delivered'
    return order.order_status !== 'delivered'
  })
})

const deliveredOrders = computed(() => {
  return orders.value.filter(order => {
    // Pickup orders are completed when status is 'picked_up'
    if (order.delivery_type === 'pickup') {
      return order.order_status === 'picked_up'
    }
    // Delivery orders are completed when status is 'delivered'
    return order.order_status === 'delivered'
  })
})

const filteredDeliveredOrders = computed(() => {
  if (!searchQuery.value) {
    return deliveredOrders.value
  }
  
  const query = searchQuery.value.toLowerCase()
  return deliveredOrders.value.filter(order => {
    return (
      order.tracking_number?.toLowerCase().includes(query) ||
      order.customer?.name?.toLowerCase().includes(query) ||
      order.service?.service_name?.toLowerCase().includes(query)
    )
  })
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const statusOrder = {
  'pending': 0,
  'approved': 1,
  'processing': 2,
  'pending_payment': 2.5,
  'completed': 3,
  'ready_for_pickup': 4,
  'picked_up': 5,
  'for_delivery': 4,
  'delivered': 5
}

const isStatusDisabled = (currentStatus, optionStatus) => {
  // Disable if trying to select the current status
  if (currentStatus === optionStatus) {
    return true
  }
  
  // Disable if trying to go backwards in the workflow
  const currentOrder = statusOrder[currentStatus]
  const optionOrder = statusOrder[optionStatus]
  
  return optionOrder < currentOrder
}

const isPaymentPending = (order) => {
  // Prevent progression if online payment is not yet received
  return order.payment_method === 'online' && !order.paid_at && order.weight_kg
}

const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'approved': 'bg-blue-100 text-blue-800',
    'processing': 'bg-purple-100 text-purple-800',
    'pending_payment': 'bg-rose-100 text-rose-800',
    'completed': 'bg-indigo-100 text-indigo-800',
    'ready_for_pickup': 'bg-teal-100 text-teal-800',
    'picked_up': 'bg-green-100 text-green-800',
    'for_delivery': 'bg-orange-100 text-orange-800',
    'delivered': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status) => {
  const formats = {
    'pending': 'Pending',
    'approved': 'Approved',
    'processing': 'Processing',
    'pending_payment': 'Pending Payment',
    'completed': 'Completed',
    'ready_for_pickup': 'Ready for Pick-up',
    'picked_up': 'Picked Up',
    'for_delivery': 'For Delivery',
    'delivered': 'Delivered',
    'cancelled': 'Cancelled'
  }
  return formats[status] || status
}

const fetchOrders = async () => {
  try {
    const response = await api.get('/shop/orders')
    orders.value = response.data
  } catch (error) {
    console.error('Error fetching orders:', error)
  } finally {
    loading.value = false
  }
}

const updateStatus = async (order, newStatus) => {
  // If changing to processing, show weight modal
  if (newStatus === 'processing') {
    selectedOrder.value = order
    showWeightModal.value = true
    return
  }

  try {
    await api.put(`/shop/orders/${order.id}/status`, {
      order_status: newStatus
    })
    
    // Update the order status in the local state
    order.order_status = newStatus
    
    // Show success message
    const statusName = formatStatus(newStatus)
    alert(`Order status updated to ${statusName}`)
  } catch (error) {
    console.error('Error updating status:', error)
    alert('Failed to update order status')
    // Refresh orders to revert the change
    fetchOrders()
  }
}

const submitWeight = async () => {
  if (!weightInput.value || parseFloat(weightInput.value) < 0.1) {
    alert('Please enter a valid weight (minimum 0.1 kg)')
    return
  }

  try {
    const response = await api.put(`/shop/orders/${selectedOrder.value.id}/status`, {
      order_status: 'processing',
      weight_kg: parseFloat(weightInput.value)
    })
    
    // Update the order in local state
    const orderIndex = orders.value.findIndex(o => o.id === selectedOrder.value.id)
    if (orderIndex !== -1) {
      orders.value[orderIndex] = { ...orders.value[orderIndex], ...response.data.order }
    }
    
    closeWeightModal()
    alert('Order marked as processing. Customer has been notified of the final amount.')
    
    // Refresh orders to get updated data
    fetchOrders()
  } catch (error) {
    console.error('Error updating order:', error)
    alert('Failed to update order status')
  }
}

const cancelOrder = async (order) => {
  if (!confirm(`Are you sure you want to cancel order ${order.tracking_number}?`)) {
    return
  }

  try {
    await api.delete(`/shop/orders/${order.id}`)
    
    // Update the order status in local state
    order.order_status = 'cancelled'
    
    alert('Order cancelled successfully. Customer has been notified.')
    
    // Refresh orders to get updated data
    fetchOrders()
  } catch (error) {
    console.error('Error cancelling order:', error)
    alert(error.response?.data?.message || 'Failed to cancel order')
  }
}

const closeWeightModal = () => {
  showWeightModal.value = false
  selectedOrder.value = null
  weightInput.value = ''
}

onMounted(() => {
  fetchOrders()
})

onBeforeUnmount(() => {
})
</script>
