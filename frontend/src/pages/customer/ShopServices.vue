<template>
  <div>
    <div class="flex items-center mb-6 sm:mb-8">
      <button
        @click="$router.back()"
        class="mr-4 text-spnr-blue-900 hover:text-spnr-blue-700"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </button>
      <div v-if="shop">
        <div class="flex items-center gap-2">
          <h1 class="text-xl sm:text-2xl font-bold text-gray-800">{{ shop.shop_name }}</h1>
          <!-- Verified Badge -->
          <span 
            v-if="shop.verification_status === 'approved'" 
            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
            title="Verified Shop"
          >
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Verified
          </span>
        </div>
        <p class="text-gray-600 text-sm">{{ shop.description }}</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
      <p class="mt-4 text-gray-600">Loading services...</p>
    </div>

    <!-- Services Grid -->
    <div v-else-if="services.length > 0" class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 md:gap-6">
      <div
        v-for="service in services"
        :key="service.id"
        class="card overflow-hidden border border-blue-300 p-3 sm:p-4 lg:p-6 flex flex-col"
      >
        <div class="mb-3 sm:mb-4">
          <div class="flex justify-between items-start mb-2 sm:mb-3">
            <div class="flex-1">
              <h3 class="text-sm sm:text-base lg:text-xl font-semibold text-gray-800 mb-1 sm:mb-2">{{ service.service_name }}</h3>
              <span class="inline-block px-2 sm:px-3 py-0.5 sm:py-1 bg-spnr-blue-100 text-spnr-blue-900 text-xs sm:text-sm rounded-full">
                {{ service.category?.name }}
              </span>
            </div>
            <div class="text-right ml-2 sm:ml-4">
              <p class="text-lg sm:text-2xl lg:text-3xl font-bold text-spnr-blue-900">₱{{ service.price }}</p>
              <p class="text-xs sm:text-sm text-gray-600">/kg</p>
            </div>
          </div>
          <p class="text-gray-700 text-xs sm:text-sm lg:text-base leading-relaxed">{{ service.description }}</p>
        </div>

        <button
          @click="openBookingModal(service)"
          class="mt-auto w-full btn-primary text-sm sm:text-base py-2 sm:py-2.5 lg:py-3 rounded-lg"
        >
          Book Now
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
      </svg>
      <p class="text-gray-600">No services available at this shop</p>
    </div>

    <!-- Booking Modal -->
    <div
      v-if="showBookingModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto"
      @click.self="closeBookingModal"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-5 my-4">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-lg font-bold text-gray-800">{{ selectedService?.service_name }}</h2>
            <p class="text-xs text-gray-600">{{ shop?.shop_name }}</p>
          </div>
          <div class="text-right">
            <p class="text-xl font-bold text-spnr-blue-900">₱{{ selectedService?.price }}<span class="text-sm text-gray-600">/kg</span></p>
          </div>
        </div>

        <form @submit.prevent="handleBooking" class="space-y-3">
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Delivery Type <span class="text-red-500">*</span></label>
            <select
              v-model="bookingForm.delivery_type"
              required
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-500 focus:border-transparent"
              @change="handleDeliveryTypeChange"
            >
              <option value="">Select delivery type</option>
              <option value="pickup">Drop-off / Pick-up</option>
              <option value="delivery">Home Delivery (+₱50)</option>
            </select>
          </div>

          <div v-if="bookingForm.delivery_type === 'delivery'" class="p-3 bg-gray-50 rounded-lg">
            <label class="block text-xs font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
            <textarea
              v-model="bookingForm.delivery_address"
              required
              rows="2"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white resize-none"
              placeholder="From your profile"
              readonly
            ></textarea>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Contact Number <span class="text-red-500">*</span></label>
            <input
              v-model="bookingForm.contact_number"
              type="tel"
              required
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-500 focus:border-transparent"
              placeholder="09XXXXXXXXX"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
            <div class="space-y-2">
              <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                <input
                  type="radio"
                  v-model="bookingForm.payment_method"
                  value="cash"
                  required
                  class="w-4 h-4 text-spnr-blue-600 flex-shrink-0"
                />
                <span class="ml-2 text-sm font-medium text-gray-900">
                  {{ bookingForm.delivery_type === 'pickup' ? 'Pay Over the Counter' : 'Cash on Delivery' }}
                </span>
              </label>
              <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                <input
                  type="radio"
                  v-model="bookingForm.payment_method"
                  value="online"
                  required
                  class="w-4 h-4 text-spnr-blue-600 flex-shrink-0"
                />
                <span class="ml-2 text-sm font-medium text-gray-900">Pay Online</span>
              </label>
            </div>
          </div>

          <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-xs">
            {{ error }}
          </div>

          <div class="flex gap-2 pt-2">
            <button
              type="button"
              @click="closeBookingModal"
              class="flex-1 px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="flex-1 px-4 py-2 text-sm bg-spnr-blue-600 text-white rounded-lg hover:bg-spnr-blue-700 transition-colors disabled:opacity-50"
            >
              {{ submitting ? 'Booking...' : 'Confirm' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const loading = ref(true)
const services = ref([])
const shop = ref(null)
const showBookingModal = ref(false)
const selectedService = ref(null)
const submitting = ref(false)
const error = ref('')

const bookingForm = ref({
  delivery_type: '',
  pickup_address: '',
  delivery_address: '',
  contact_number: '',
  payment_method: '',
})

const fetchServices = async () => {
  try {
    const shopId = route.params.id
    console.log('Fetching services for shop:', shopId)
    const response = await api.get(`/customer/services/${shopId}`)
    console.log('Services response:', response.data)
    services.value = response.data
    if (services.value.length > 0) {
      shop.value = services.value[0].shop
    }
  } catch (error) {
    console.error('Error fetching services:', error)
  } finally {
    loading.value = false
  }
}

const openBookingModal = (service) => {
  selectedService.value = service
  showBookingModal.value = true
  // Pre-populate contact number from user profile
  const user = authStore.user || {}
  bookingForm.value.contact_number = user.phone || ''
}

const handleDeliveryTypeChange = () => {
  if (bookingForm.value.delivery_type === 'delivery') {
    // Build full address from profile
    const user = authStore.user || {}
    const addressParts = [
      user.house_number,
      user.street,
      user.barangay,
      user.city,
      user.province,
      user.postal_code
    ].filter(Boolean)
    
    const fullAddress = addressParts.join(', ')
    bookingForm.value.pickup_address = fullAddress
    bookingForm.value.delivery_address = fullAddress
  } else {
    bookingForm.value.pickup_address = ''
    bookingForm.value.delivery_address = ''
  }
}

const closeBookingModal = () => {
  showBookingModal.value = false
  selectedService.value = null
  bookingForm.value = {
    delivery_type: '',
    pickup_address: '',
    delivery_address: '',
    contact_number: '',
    payment_method: '',
  }
  error.value = ''
}

const handleBooking = async () => {
  submitting.value = true
  error.value = ''

  try {
    const payload = {
      shop_id: shop.value.id,
      service_id: selectedService.value.id,
      delivery_type: bookingForm.value.delivery_type,
      contact_number: bookingForm.value.contact_number,
      payment_method: bookingForm.value.payment_method,
    }

    // Only include addresses for delivery type
    if (bookingForm.value.delivery_type === 'delivery') {
      payload.pickup_address = bookingForm.value.pickup_address
      payload.delivery_address = bookingForm.value.delivery_address
    }

    await api.post('/customer/book', payload)

    closeBookingModal()
    router.push('/customer/orders')
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to book service. Please try again.'
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  fetchServices()
})
</script>
