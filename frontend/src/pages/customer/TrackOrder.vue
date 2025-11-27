<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Track Your Order</h1>
      <p class="text-sm text-gray-600 mt-1">Enter your tracking number to view order status and timeline</p>
    </div>

    <div class="max-w-3xl mx-auto">
      <div v-if="!isCompact">
        <!-- Tracking Form -->
        <div class="bg-gradient-to-br from-spnr-blue-50 to-white rounded-xl shadow-sm border border-spnr-blue-200 p-6 mb-6">
          <form @submit.prevent="handleTrack" class="space-y-4">
            <div>
              <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">
                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                Tracking Number
              </label>
              <div class="relative">
                <input
                  id="tracking_number"
                  v-model="trackingNumber"
                  type="text"
                  required
                  class="w-full px-4 py-3 pl-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-500 focus:border-transparent"
                  placeholder="SPNR-ABC1234567"
                />
                <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
            </div>

            <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-start">
              <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
              <span>{{ error }}</span>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full px-4 py-3 bg-spnr-blue-600 text-white font-medium rounded-lg hover:bg-spnr-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Tracking...' : 'Track Order' }}
            </button>
          </form>
        </div>

        <!-- Order Details -->
        <div v-if="order" class="bg-white rounded-xl shadow-md border border-gray-400 overflow-hidden">
          <!-- Header with Status -->
          <div class="bg-gradient-to-r from-spnr-blue-600 to-spnr-blue-700 px-6 py-4">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-xl font-bold text-white">Order Details</h2>
                <p class="text-spnr-blue-100 text-sm mt-1">{{ order.tracking_number }}</p>
              </div>
              <span
                :class="{
                  'bg-yellow-400 text-yellow-900': order.order_status === 'pending',
                  'bg-blue-400 text-blue-900': order.order_status === 'approved' || order.order_status === 'processing',
                  'bg-orange-400 text-orange-900': order.order_status === 'ready_for_pickup',
                  'bg-purple-400 text-purple-900': order.order_status === 'for_delivery',
                  'bg-green-400 text-green-900': order.order_status === 'delivered' || order.order_status === 'picked_up',
                  'bg-red-400 text-red-900': order.order_status === 'cancelled',
                }"
                class="px-4 py-2 rounded-full text-sm font-bold shadow-lg"
              >
                {{ formatStatus(order.order_status) }}
              </span>
            </div>
          </div>

          <!-- Order Info -->
          <div class="p-6">
            <!-- Service & Shop Info -->         
             <div class="bg-spnr-blue-50 rounded-lg p-4 mb-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <div class="flex items-center text-xs text-gray-600 mb-1">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    Service
                  </div>
                  <div class="font-semibold text-gray-900">{{ order.service?.service_name }}</div>
                </div>
                <div>
                  <div class="flex items-center text-xs text-gray-600 mb-1">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                    </svg>
                    Shop
                  </div>
                  <div class="font-semibold text-gray-900">{{ order.shop?.shop_name }}</div>
                </div>
              </div>
            </div>

            <!-- Amount & Weight -->
            <div class="bg-gradient-to-r from-spnr-blue-700 to-spnr-blue-600 rounded-lg p-4 mb-4 text-white">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <div class="text-spnr-blue-100 text-xs mb-1">Weight</div>
                  <div class="text-2xl font-bold">{{ order.weight_kg }} kg</div>
                </div>
                  <div v-if="order.weight_kg" class="text-right">
                    <div class="text-spnr-blue-100 text-xs mb-1">Total Amount</div>
                    <div class="text-2xl font-bold">₱{{ order.final_amount || order.total_amount }}</div>
                    <div v-if="order.delivery_fee > 0" class="text-xs text-spnr-blue-100 mt-1">
                      Includes ₱{{ order.delivery_fee }} delivery fee
                  </div>
                </div>
              </div>
            </div>
            <!-- Delivery Info -->
            <div class="space-y-3 mb-4">
              <div class="flex items-start">
                <svg class="w-5 h-5 text-spnr-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                  <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                </svg>
                <div class="flex-1">
                  <div class="text-xs text-gray-600">Delivery Type</div>
                  <div class="font-semibold text-gray-900">{{ order.delivery_type === 'pickup' ? 'Drop-off/Pick-up at Shop' : 'Home Delivery' }}</div>
                </div>
              </div>

              <div class="flex items-start">
                <svg class="w-5 h-5 text-spnr-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                </svg>
                <div class="flex-1">
                  <div class="text-xs text-gray-600">Contact Number</div>
                  <div class="font-semibold text-gray-900">{{ order.contact_number }}</div>
                </div>
              </div>

              <div v-if="order.delivery_type === 'delivery'" class="flex items-start">
                <svg class="w-5 h-5 text-spnr-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                  <div class="text-xs text-gray-600">Pickup Address</div>
                  <div class="font-medium text-gray-900 text-sm">{{ order.pickup_address }}</div>
                </div>
              </div>

              <div v-if="order.delivery_type === 'delivery'" class="flex items-start">
                <svg class="w-5 h-5 text-spnr-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                  <div class="text-xs text-gray-600">Delivery Address</div>
                  <div class="font-medium text-gray-900 text-sm">{{ order.delivery_address }}</div>
                </div>
              </div>
            </div>

            <!-- Payment Info -->
            <div class="border-t pt-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-spnr-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                  </svg>
                  <div>
                    <div class="text-xs text-gray-600">Payment Method</div>
                    <div class="font-semibold text-gray-900">{{ order.payment_method === 'cash' ? (order.delivery_type === 'pickup' ? 'Pay Over the Counter' : 'Cash on Delivery') : 'Online Payment' }}</div>
                  </div>
                </div>
                <div v-if="order.paid_at" class="flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-sm font-semibold">Paid</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Status Timeline -->
          <div class="bg-gray-50 px-6 py-5 border-t">
            <div class="flex items-center mb-4">
              <svg class="w-5 h-5 text-spnr-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
              </svg>
              <h3 class="text-lg font-semibold text-gray-800">Order Timeline</h3>
            </div>
            <div class="space-y-4 relative">
              <div
                v-for="(status, index) in statusTimeline"
                :key="index"
                class="flex items-start relative"
              >
                <!-- Connecting Line -->
                <div v-if="index < statusTimeline.length - 1" 
                     :class="status.completed ? 'bg-spnr-blue-900' : 'bg-gray-300'"
                     class="absolute left-4 top-10 w-0.5 h-full -ml-px"></div>
                
                <div class="flex-shrink-0 relative z-10">
                  <div
                    :class="status.completed ? 'bg-spnr-blue-900 ring-4 ring-spnr-blue-100' : 'bg-gray-300'"
                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all"
                  >
                    <svg v-if="status.completed" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span v-else class="w-3 h-3 bg-white rounded-full"></span>
                  </div>
                </div>
                <div class="ml-4 flex-1 pb-4">
                  <p :class="status.completed ? 'text-gray-800 font-semibold' : 'text-gray-500'" class="text-sm">
                    {{ status.label }}
                  </p>
                  <p v-if="status.timestamp" class="text-xs text-gray-500 mt-1 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    {{ formatDateTime(status.timestamp) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Compact mobile/tablet UI -->
      <div v-else class="space-y-4">
        <!-- Compact Tracking Form -->
        <div class="bg-gradient-to-br from-spnr-blue-50 to-white rounded-lg shadow-sm border border-spnr-blue-200 p-4">
          <form @submit.prevent="handleTrack" class="space-y-3">
            <div>
              <label for="tracking_number_mobile" class="block text-sm font-medium text-gray-700 mb-1">
                <svg class="w-4 h-4 inline mr-1 text-spnr-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/>
                </svg>
                Tracking Number
              </label>
              <div class="relative">
                <input
                  id="tracking_number_mobile"
                  v-model="trackingNumber"
                  type="text"
                  required
                  class="w-full px-3 py-2 pl-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-500 focus:border-transparent"
                  placeholder="SPNR-ABC1234567"
                />
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
            </div>

            <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded text-sm flex items-start">
              <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
              <span class="text-sm">{{ error }}</span>
            </div>

            <div class="flex items-center justify-end">
              <button
                type="submit"
                :disabled="loading"
                class="px-10 py-2 bg-spnr-blue-600 text-white text-sm rounded-lg hover:bg-spnr-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ loading ? 'Tracking...' : 'Track' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Compact Order Card -->
        <div v-if="order" class="bg-white rounded-lg shadow border overflow-hidden">
          <div class="bg-gradient-to-r from-spnr-blue-600 to-spnr-blue-700 px-3 py-2 flex items-center justify-between">
            <div class="text-sm font-bold text-white truncate">{{ order.tracking_number }}</div>
            <span
              :class="{
                'bg-yellow-400 text-yellow-900': order.order_status === 'pending',
                'bg-blue-400 text-blue-900': order.order_status === 'approved' || order.order_status === 'processing',
                'bg-orange-400 text-orange-900': order.order_status === 'ready_for_pickup',
                'bg-purple-400 text-purple-900': order.order_status === 'for_delivery',
                'bg-green-400 text-green-900': order.order_status === 'delivered' || order.order_status === 'picked_up',
                'bg-red-400 text-red-900': order.order_status === 'cancelled',
              }"
              class="px-3 py-1 rounded-full text-xs font-bold"
            >
              {{ formatStatus(order.order_status) }}
            </span>
          </div>

          <div class="p-4">
            <div class="text-xs text-gray-500 mb-2 flex items-center">
              <svg class="w-4 h-4 mr-1 text-spnr-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
              </svg>
              <div class="truncate">{{ order.shop?.shop_name }}</div>
            </div>

            <div class="grid grid-cols-1 gap-2 mb-3">
              <div class="flex items-center justify-between bg-spnr-blue-50 rounded">
                <div class="flex items-center text-xs text-gray-600">
                  <svg class="w-4 h-4 mr-1 text-spnr-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                  </svg>
                  Service
                </div>
                <div class="font-semibold text-gray-900 text-sm">{{ order.service?.service_name }}</div>
              </div>
              <div class="flex items-center justify-between bg-spnr-blue-50 rounded p-2">
                <div class="text-xs text-gray-600">Total</div>
                <div class="font-bold text-gray-900 text-sm">₱{{ order.final_amount || order.total_amount }}</div>
              </div>
            </div>

            <div class="text-sm text-gray-800 space-y-2 mb-3">
              <div class="flex items-start">
                <svg class="w-4 h-4 text-spnr-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                  <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                </svg>
                <div>
                  <div class="text-xs text-gray-600">Delivery</div>
                  <div class="font-medium">{{ order.delivery_type === 'pickup' ? 'Drop-off/Pick-up at Shop' : 'Home Delivery' }}</div>
                </div>
              </div>

              <div class="flex items-start">
                <svg class="w-4 h-4 text-spnr-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                </svg>
                <div>
                  <div class="text-xs text-gray-600">Contact</div>
                  <div class="font-medium">{{ order.contact_number }}</div>
                </div>
              </div>

              <div v-if="order.delivery_type === 'delivery'" class="text-sm">
                <div class="text-xs text-gray-600">Pickup</div>
                <div class="font-medium text-sm">{{ order.pickup_address }}</div>
                <div class="text-xs text-gray-600 mt-1">Delivery</div>
                <div class="font-medium text-sm">{{ order.delivery_address }}</div>
              </div>
            </div>

            <div class="border-t pt-3 text-sm">
              <div class="flex items-center justify-between">
                <div>
                  <div class="flex items-center text-xs text-gray-600">
                    <svg class="w-4 h-4 mr-2 text-spnr-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                      <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Payment
                  </div>
                  <div class="font-semibold">{{ order.payment_method === 'cash' ? (order.delivery_type === 'pickup' ? 'Pay Over the Counter' : 'Cash on Delivery') : 'Online Payment' }}</div>
                </div>
                <div v-if="order.paid_at" class="flex items-center bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  Paid
                </div>
              </div>
            </div>

            <div class="mt-3">
              <h4 class="text-sm font-semibold mb-2">Order Timeline</h4>
              <div class="space-y-3">
                <div v-for="(status, index) in statusTimeline" :key="index" class="flex items-start gap-3">
                  <div :class="status.completed ? 'bg-spnr-blue-900' : 'bg-gray-300'" class="w-3 h-3 rounded-full mt-1 flex-shrink-0"></div>
                  <div>
                    <div :class="status.completed ? 'font-semibold text-gray-800' : 'text-gray-500'" class="text-sm">{{ status.label }}</div>
                    <div v-if="status.timestamp" class="text-xs text-gray-500">{{ formatDateTime(status.timestamp) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import api from '../../services/api'

const trackingNumber = ref('')
const loading = ref(false)
const error = ref('')
const order = ref(null)
const isCompact = ref(false)

const updateCompact = () => {
  isCompact.value = window.innerWidth < 1024
}

const statusTimeline = computed(() => {
  if (!order.value) return []
  
  // Different timeline for pickup vs delivery orders
  if (order.value.delivery_type === 'pickup') {
    const pickupStatuses = ['pending', 'approved', 'processing', 'pending_payment', 'ready_for_pickup', 'picked_up']
    const currentIndex = pickupStatuses.indexOf(order.value.order_status)
    
    const allStatuses = [
      { label: 'Order Placed', status: 'pending', completed: currentIndex >= 0, timestamp: order.value.created_at },
      { label: 'Order Approved', status: 'approved', completed: currentIndex >= 1, timestamp: order.value.approved_at },
      { label: 'Processing Laundry', status: 'processing', completed: currentIndex >= 2 || currentIndex >= 3, timestamp: order.value.processing_at },
      { label: 'Pending Payment', status: 'pending_payment', completed: order.value.pending_payment_at && order.value.paid_at ? true : currentIndex === 3, timestamp: order.value.pending_payment_at },
      { label: 'Ready for Pick-up', status: 'ready_for_pickup', completed: currentIndex >= 4, timestamp: order.value.ready_for_pickup_at },
      { label: 'Picked Up', status: 'picked_up', completed: currentIndex >= 5, timestamp: order.value.picked_up_at },
    ]
    
    // Only return statuses that have timestamps
    return allStatuses.filter(status => status.timestamp)
  } else {
    // Delivery order timeline
    const deliveryStatuses = ['pending', 'approved', 'processing', 'pending_payment', 'for_delivery', 'delivered']
    const currentIndex = deliveryStatuses.indexOf(order.value.order_status)
    
    const allStatuses = [
      { label: 'Order Placed', status: 'pending', completed: currentIndex >= 0, timestamp: order.value.created_at },
      { label: 'Order Approved', status: 'approved', completed: currentIndex >= 1, timestamp: order.value.approved_at },
      { label: 'Processing Laundry', status: 'processing', completed: currentIndex >= 2 || currentIndex >= 3, timestamp: order.value.processing_at },
      { label: 'Pending Payment', status: 'pending_payment', completed: order.value.pending_payment_at && order.value.paid_at ? true : currentIndex === 3, timestamp: order.value.pending_payment_at },
      { label: 'Out for Delivery', status: 'for_delivery', completed: currentIndex >= 4, timestamp: order.value.for_delivery_at },
      { label: 'Delivered', status: 'delivered', completed: currentIndex >= 5, timestamp: order.value.delivered_at },
    ]
    
    // Only return statuses that have timestamps
    return allStatuses.filter(status => status.timestamp)
  }
})

const handleTrack = async () => {
  loading.value = true
  error.value = ''
  order.value = null

  try {
    const response = await api.get(`/customer/track/${trackingNumber.value}`)
    order.value = response.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Order not found. Please check your tracking number.'
  } finally {
    loading.value = false
  }
}

const formatStatus = (status) => {
  return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const formatDateTime = (timestamp) => {
  if (!timestamp) return ''
  const date = new Date(timestamp)
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
}

onMounted(() => {
  updateCompact()
  window.addEventListener('resize', updateCompact)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateCompact)
})
</script>
