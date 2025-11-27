<template>
  <div class="max-w-xl mx-auto px-4 sm:px-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Payment</h1>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-b-2 border-spnr-blue-900"></div>
      <p class="mt-4 text-sm sm:text-base text-gray-600">Loading order details...</p>
    </div>

    <!-- Order Details -->
    <div v-else-if="order" class="space-y-4 sm:space-y-5">
      <!-- Order Summary Card -->
      <div class="card p-4 sm:p-5">
        <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Order Summary</h2>
        <div class="space-y-2 sm:space-y-2.5">
          <div class="flex justify-between text-sm sm:text-base">
            <span class="text-gray-600">Service:</span>
            <span class="font-medium">{{ order.service?.service_name }}</span>
          </div>
          <div class="flex justify-between text-sm sm:text-base">
            <span class="text-gray-600">Shop:</span>
            <span class="font-medium">{{ order.shop?.shop_name }}</span>
          </div>
          <div class="flex justify-between text-sm sm:text-base">
            <span class="text-gray-600">Tracking Number:</span>
            <span class="font-medium">{{ order.tracking_number }}</span>
          </div>
          <div class="flex justify-between text-sm sm:text-base">
            <span class="text-gray-600">Weight:</span>
            <span class="font-medium">{{ order.weight_kg }} kg</span>
          </div>
          <div class="flex justify-between text-sm sm:text-base">
            <span class="text-gray-600">Price per kg:</span>
            <span class="font-medium">₱{{ order.service?.price }}</span>
          </div>
          <div v-if="order.delivery_fee > 0" class="flex justify-between text-sm sm:text-base">
            <span class="text-gray-600">Delivery Fee:</span>
            <span class="font-medium">₱{{ order.delivery_fee }}</span>
          </div>
          <div class="border-t pt-2.5 sm:pt-3 flex justify-between text-base sm:text-lg">
            <span class="font-semibold text-gray-900">Total Amount:</span>
            <span class="font-bold text-spnr-blue-900">₱{{ order.final_amount }}</span>
          </div>
        </div>
      </div>

      <!-- Payment Method Selection -->
      <div class="card p-3 sm:p-3">
        <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-2 sm:mb-3">Select Payment Method</h2>
        
        <div class="space-y-2">
          <label class="flex items-center p-2.5 sm:p-3 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                 :class="{ 'border-spnr-blue-600 bg-spnr-blue-50': paymentMethod === 'gcash' }">
            <input
              type="radio"
              v-model="paymentMethod"
              value="gcash"
              class="w-4 h-4 text-spnr-blue-600"
            />
            <div class="ml-2.5 flex-1 flex items-center justify-between">
              <span class="font-medium text-gray-900 text-sm sm:text-base">GCash</span>
              <img src="/images/gcash-logo.png" alt="GCash" class="h-5 sm:h-6" />
            </div>
          </label>

          <label class="flex items-center p-2.5 sm:p-3 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                 :class="{ 'border-spnr-blue-600 bg-spnr-blue-50': paymentMethod === 'paymaya' }">
            <input
              type="radio"
              v-model="paymentMethod"
              value="paymaya"
              class="w-4 h-4 text-spnr-blue-600"
            />
            <div class="ml-2.5 flex-1 flex items-center justify-between">
              <span class="font-medium text-gray-900 text-sm sm:text-base">Maya</span>
              <img src="/images/maya-logo.png" alt="Maya" class="h-5 sm:h-6" />
            </div>
          </label>

          <label class="flex items-center p-2.5 sm:p-3 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                 :class="{ 'border-spnr-blue-600 bg-spnr-blue-50': paymentMethod === 'card' }">
            <input
              type="radio"
              v-model="paymentMethod"
              value="card"
              class="w-4 h-4 text-spnr-blue-600"
            />
            <div class="ml-2.5 flex-1 flex items-center justify-between">
              <span class="font-medium text-gray-900 text-sm sm:text-base">Credit/Debit Card</span>
              <div class="flex gap-1.5 sm:gap-2">
                <img src="/images/visa-logo.png" alt="Visa" class="h-5 sm:h-6" />
                <img src="/images/mastercard-logo.png" alt="Mastercard" class="h-5 sm:h-6" />
              </div>
            </div>
          </label>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg text-sm sm:text-base">
        {{ error }}
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-2 sm:gap-3">
        <button
          @click="$router.back()"
          class="flex-1 btn-outline text-sm sm:text-base py-2.5 sm:py-3"
        >
          Cancel
        </button>
        <button
          @click="processPayment"
          :disabled="!paymentMethod || processing"
          class="flex-1 btn-primary disabled:opacity-50 text-sm sm:text-base py-2.5 sm:py-3"
        >
          {{ processing ? 'Processing...' : `Pay ₱${order.final_amount}` }}
        </button>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-12">
      <svg class="w-12 sm:w-16 h-12 sm:h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <p class="text-sm sm:text-base text-gray-600 mb-4">Order not found or not ready for payment</p>
      <router-link to="/customer/orders" class="btn-primary text-sm sm:text-base">
        Back to Orders
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const router = useRouter()

const loading = ref(true)
const processing = ref(false)
const order = ref(null)
const paymentMethod = ref('')
const error = ref('')

const fetchOrder = async () => {
  try {
    const orderId = route.params.id
    const response = await api.get(`/customer/orders`)
    const allOrders = response.data
    order.value = allOrders.find(o => o.id == orderId)
    
    // Validate order is ready for payment
    if (order.value && (!order.value.weight_kg || order.value.paid_at)) {
      order.value = null
    }
  } catch (err) {
    console.error('Error fetching order:', err)
    order.value = null
  } finally {
    loading.value = false
  }
}

const processPayment = async () => {
  processing.value = true
  error.value = ''

  try {
    const response = await api.post('/customer/payment/process', {
      order_id: order.value.id,
      payment_method: paymentMethod.value,
      amount: order.value.final_amount
    })

    // Payment successful
    alert('Payment successful!')
    router.push('/customer/orders')
  } catch (err) {
    error.value = err.response?.data?.message || 'Payment failed. Please try again.'
  } finally {
    processing.value = false
  }
}

onMounted(() => {
  fetchOrder()
})
</script>
