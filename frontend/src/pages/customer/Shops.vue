<template>
  <div>
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Browse Laundry Shops</h1>

    <!-- Search Bar -->
    <div class="mb-6 sm:mb-8">
      <div class="relative max-w-xl">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by shop name, location, or owner..."
          class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-spnr-blue-300 focus:border-transparent"
        />
        <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
      <p class="mt-4 text-gray-600">Loading shops...</p>
    </div>

    <!-- Shops Grid -->
    <div v-else-if="filteredShops.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="shop in filteredShops"
        :key="shop.id"
        class="card bg-gradient-to-b from-spnr-blue-100 to-white p-6 hover:shadow-xl transition-shadow overflow-hidden border border-blue-300 flex flex-col"
      >
        <!-- Shop Photo -->
        <div class="w-full h-36 sm:h-48 bg-gray-200 mb-3 -mt-4 -mx-4">
          <img 
            v-if="shop.photo_path" 
            :src="shop.photo_path" 
            :alt="shop.shop_name"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-spnr-blue-100 to-spnr-blue-200">
            <svg class="w-20 h-20 text-spnr-blue-900 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
        </div>

        <div class="flex items-start justify-between mb-3">
          <div class="flex-1">
            <div class="flex items-center gap-2">
              <h3 class="text-lg sm:text-xl font-semibold text-gray-800">{{ shop.shop_name }}</h3>
              <!-- Verified Badge -->
              <span 
                v-if="shop.verification_status === 'approved'" 
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                title="Verified Shop"
              >
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Verified
              </span>
            </div>
            <p class="text-sm text-gray-600 mt-1">{{ shop.owner?.name }}</p>
          </div>
        </div>

        <p class="text-gray-700 mb-2 text-sm line-clamp-2">{{ shop.description }}</p>

        <div class="space-y-2 text-sm text-gray-600 mb-4 flex-grow">
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-2 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>{{ shop.address }}</span>
          </div>
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-2 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            <span>{{ shop.contact_number }}</span>
          </div>
        </div>

        <div class="mt-auto pt-3 flex gap-2 flex-wrap items-center">
          <button
            @click="viewServices(shop.id)"
            class="flex-1 min-w-0 btn-primary px-3 py-2 text-sm rounded-md"
          >
            View Services
          </button>
          <button
            @click="viewDetails(shop.id)"
            class="flex-1 min-w-0 px-3 py-2 text-sm font-medium rounded-md bg-white text-spnr-blue-900 border-2 border-spnr-blue-900 hover:bg-spnr-blue-50 transition-colors"
          >
            View Details
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
      </svg>
      <p class="text-gray-600">{{ searchQuery ? 'No shops found matching your search' : 'No laundry shops available at the moment' }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const loading = ref(true)
const shops = ref([])
const searchQuery = ref('')

const filteredShops = computed(() => {
  if (!searchQuery.value) {
    return shops.value
  }
  
  const query = searchQuery.value.toLowerCase()
  return shops.value.filter(shop => {
    return (
      shop.shop_name?.toLowerCase().includes(query) ||
      shop.address?.toLowerCase().includes(query) ||
      shop.owner?.name?.toLowerCase().includes(query) ||
      shop.description?.toLowerCase().includes(query)
    )
  })
})

const fetchShops = async () => {
  try {
    const response = await api.get('/customer/shops')
    shops.value = response.data
  } catch (error) {
    console.error('Error fetching shops:', error)
  } finally {
    loading.value = false
  }
}

const viewServices = (shopId) => {
  router.push(`/customer/shops/${shopId}/services`)
}

const viewDetails = (shopId) => {
  router.push(`/customer/shops/${shopId}/details`)
}

onMounted(() => {
  fetchShops()
})
</script>
