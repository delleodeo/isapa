<template>
  <div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Browse Services</h1>
        <p class="text-gray-600">Find the perfect laundry service for your needs</p>
      </div>

      <!-- Filter and Search Bar -->
      <div class="mb-6 flex flex-col sm:flex-row gap-3">
        <!-- Category Filter Dropdown -->
        <div class="relative w-full sm:w-72">
          <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-500 pointer-events-none z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
          </svg>
          <select
            v-model="selectedCategory"
            class="w-full pl-12 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-500 focus:border-transparent bg-white appearance-none cursor-pointer font-medium text-gray-700 hover:border-gray-400 transition-colors"
          >
            <option :value="null" class="font-semibold">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id" class="py-2">
              {{ category.name }} • {{ category.services_count || 0 }} service{{ (category.services_count || 0) !== 1 ? 's' : '' }}
            </option>
          </select>
          <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </div>

        <!-- Search Bar -->
        <div class="relative flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search for services, shops, or categories..."
            class="w-full px-4 py-3 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-500 focus:border-transparent"
          />
          <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
        <p class="mt-4 text-gray-600">Loading services...</p>
      </div>

      <!-- Services by Shop -->
      <div v-else-if="filteredShopServices.length > 0" class="space-y-8 ">
        <div v-for="shopData in filteredShopServices" :key="shopData.shop.id" class="bg-white rounded-xl shadow-md border border-blue-300 overflow-hidden ">
          <!-- Shop Header -->
          <div class="bg-gradient-to-b from-spnr-blue-100 to-white p-6 border-b border-gray-200 ">
            <div class="flex items-start justify-between flex-wrap gap-4">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <h2 class="text-2xl font-bold text-gray-900">{{ shopData.shop.shop_name }}</h2>
                  <span 
                    v-if="shopData.shop.verification_status === 'approved'" 
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Verified
                  </span>
                </div>
                <p v-if="shopData.shop.description" class="text-gray-600 mb-3">{{ shopData.shop.description }}</p>
                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ shopData.shop.address }}
                  </div>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ shopData.shop.contact_number }}
                  </div>
                </div>
              </div>
              <router-link 
                :to="`/customer/shops/${shopData.shop.id}/services`"
                class="ml-0 sm:ml-4 mt-2 sm:mt-0 flex-shrink-0 px-4 py-2 bg-spnr-blue-900 text-white rounded-lg hover:bg-spnr-blue-800 transition-colors font-medium text-sm whitespace-nowrap">
                View All Services
              </router-link>
            </div>
          </div>

          <!-- Services Grid -->
          <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <div 
                v-for="service in shopData.services" 
                :key="service.id" 
                class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-spnr-blue-300 hover:shadow-md transition-all flex flex-col">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="font-semibold text-gray-900 flex-1">{{ service.service_name }}</h3>
                  <span class="text-lg font-bold text-spnr-blue-900 ml-2">₱{{ service.price }}/kg</span>
                </div>
                <p v-if="service.description" class="text-sm text-gray-600 mb-3 flex-grow">{{ service.description }}</p>
                <div class="flex items-center justify-between mt-auto pt-2">
                  <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full font-medium">
                    {{ getCategoryName(service.category_id) }}
                  </span>
                  <button 
                    @click="openBookingModal(shopData.shop, service)"
                    class="text-sm text-white bg-spnr-blue-900 hover:bg-spnr-blue-800 px-3 py-1.5 rounded-lg transition-colors font-medium">
                    Book Now
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12 bg-white rounded-xl shadow-md">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-600 mb-4">No services found matching your criteria</p>
        <button 
          @click="resetFilters"
          class="px-6 py-2 bg-spnr-blue-900 text-white rounded-lg hover:bg-spnr-blue-800 transition-colors font-medium">
          Clear Filters
        </button>
      </div>
    </div>

    <!-- Booking Modal -->
    <div v-if="showBookingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto" @click.self="closeBookingModal">
      <div class="bg-white rounded-lg max-w-md w-full p-5 my-4">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-lg font-bold text-gray-800">{{ selectedService?.service_name }}</h2>
            <p class="text-xs text-gray-600">{{ selectedShop?.shop_name }}</p>
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
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const router = useRouter()
const authStore = useAuthStore()
const loading = ref(true)
const categories = ref([])
const shops = ref([])
const allServices = ref([])
const selectedCategory = ref(null)
const searchQuery = ref('')
const showBookingModal = ref(false)
const selectedShop = ref(null)
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

const fetchData = async () => {
  try {
    loading.value = true
    
    // Fetch categories
    const categoriesResponse = await api.get('/categories')
    categories.value = categoriesResponse.data
    
    // Fetch all shops
    const shopsResponse = await api.get('/shops')
    shops.value = shopsResponse.data
    
    // Fetch services for each shop
    const servicesPromises = shops.value.map(async (shop) => {
      try {
        const servicesResponse = await api.get(`/services/${shop.id}`)
        return {
          shop: shop,
          services: servicesResponse.data
        }
      } catch (error) {
        return {
          shop: shop,
          services: []
        }
      }
    })
    
    const results = await Promise.all(servicesPromises)
    allServices.value = results.filter(result => result.services.length > 0)
    
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    loading.value = false
  }
}

const filteredShopServices = computed(() => {
  let filtered = allServices.value

  // Filter by category
  if (selectedCategory.value !== null) {
    filtered = filtered.map(shopData => ({
      shop: shopData.shop,
      services: shopData.services.filter(service => service.category_id === selectedCategory.value)
    })).filter(shopData => shopData.services.length > 0)
  }

  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.map(shopData => ({
      shop: shopData.shop,
      services: shopData.services.filter(service => 
        service.service_name.toLowerCase().includes(query) ||
        service.description?.toLowerCase().includes(query) ||
        shopData.shop.shop_name.toLowerCase().includes(query)
      )
    })).filter(shopData => 
      shopData.services.length > 0 || 
      shopData.shop.shop_name.toLowerCase().includes(query)
    )
  }

  return filtered
})

const getCategoryName = (categoryId) => {
  const category = categories.value.find(cat => cat.id === categoryId)
  return category ? category.name : 'Uncategorized'
}

const resetFilters = () => {
  selectedCategory.value = null
  searchQuery.value = ''
}

const openBookingModal = (shop, service) => {
  selectedShop.value = shop
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
  selectedShop.value = null
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
      shop_id: selectedShop.value.id,
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
  fetchData()
})
</script>
