<template>
  <div>
    <!-- Back Button -->
    <button 
      @click="router.back()" 
      class="mb-6 flex items-center text-spnr-blue-900 hover:text-spnr-blue-700 transition-colors"
    >
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
      Back to Shops
    </button>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
      <p class="mt-4 text-gray-600">Loading shop details...</p>
    </div>

    <!-- Shop Details -->
    <div v-else-if="shop" class="space-y-6">
      <!-- Two Column Layout (desktop) or compact two-column (mobile) -->
      <div v-if="!isMobileTwoColumn" class="grid lg:grid-cols-2 gap-6">
        <!-- Left Column: Shop Header with Photo and Description -->
        <div class="space-y-6">
          <!-- Shop Photo and Name -->
          <div class="card overflow-hidden border border-blue-300 bg-gradient-to-b from-spnr-blue-50 to-white">
            <div class="w-full h-40 sm:h-48 md:h-56 lg:h-64 bg-gray-200 -mt-4 sm:-mt-6 -mx-4 sm:-mx-6 mb-6">
              <img 
                v-if="shop.photo_path" 
                :src="shop.photo_path" 
                :alt="shop.shop_name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-spnr-blue-100 to-spnr-blue-200">
                <svg class="w-32 h-32 text-spnr-blue-900 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
              </div>
            </div>

            <div class="flex items-start justify-between mb-4">
              <div>
                <div class="flex items-center gap-2 mb-2">
                  <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">{{ shop.shop_name }}</h1>
                  <!-- Verified Badge -->
                  <span 
                    v-if="shop.verification_status === 'approved'" 
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                    title="Verified Shop"
                  >
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Verified Shop
                  </span>
                </div>
                <p v-if="shop.verified_at" class="text-sm text-gray-500">
                  Verified on {{ formatDate(shop.verified_at) }}
                </p>
              </div>
            </div>

            <div class="prose max-w-none">
              <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-2">Description</h3>
              <p class="text-sm lg:text-base text-gray-700 leading-relaxed">{{ shop.description }}</p>
            </div>
          </div>
        </div>

        <!-- Right Column: Shop Information and Owner Information -->
        <div class="space-y-6">
          <!-- Shop Information -->
          <div class="card overflow-hidden border border-blue-300 bg-gradient-to-b from-spnr-blue-100 to-white">
            <h2 class="text-lg lg:text-xl font-semibold text-gray-800 mb-4 flex items-center">
              <svg class="w-6 h-6 mr-2 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Shop Information
            </h2>
            <div class="space-y-4">
              <!-- Address -->
              <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 text-spnr-blue-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div>
                  <p class="font-medium text-gray-700">Address</p>
                  <p class="text-sm lg:text-base text-gray-600">{{ shop.address }}</p>
                </div>
              </div>

              <!-- Contact Number -->
              <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 text-spnr-blue-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <div>
                  <p class="font-medium text-gray-700">Contact Number</p>
                  <p class="text-sm lg:text-base text-gray-600">{{ shop.contact_number }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Owner Information -->
          <div class="card overflow-hidden border border-blue-300 bg-gradient-to-b from-spnr-blue-100 to-white">
            <h2 class="text-lg lg:text-xl font-semibold text-gray-800 mb-4 flex items-center">
              <svg class="w-6 h-6 mr-2 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              Owner Information
            </h2>
            <div v-if="shop.owner" class="space-y-4">
              <!-- Owner Name -->
              <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 text-spnr-blue-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <div>
                  <p class="font-medium text-gray-700">Owner Name</p>
                  <p class="text-sm lg:text-base text-gray-600">{{ shop.owner.name }}</p>
                </div>
              </div>

              <!-- Owner Email -->
              <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 text-spnr-blue-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <div>
                  <p class="font-medium text-gray-700">Email</p>
                  <p class="text-sm lg:text-base text-gray-600">{{ shop.owner.email }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Compact two-column mobile look: keep two columns but compacted -->
      <div v-else class="grid grid-cols-2 gap-3">
        <!-- left: compact photo + title + description -->
        <div>
          <div class="card overflow-hidden border border-blue-300 p-2 bg-gradient-to-b from-spnr-blue-50 to-white">
            <div class="w-full h-28 bg-gray-200 mb-3">
              <img v-if="shop.photo_path" :src="shop.photo_path" :alt="shop.shop_name" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-spnr-blue-100 to-spnr-blue-200">
                <svg class="w-12 h-12 text-spnr-blue-900 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
              </div>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h2 class="text-sm font-semibold text-gray-800">{{ shop.shop_name }}</h2>
                <span v-if="shop.verification_status === 'approved'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" title="Verified Shop">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  Verified
                </span>
              </div>
              <p class="text-xs text-gray-600 mt-2">{{ shop.description }}</p>
            </div>
          </div>
        </div>

        <!-- right: stacked shop-info and owner cards -->
        <div class="space-y-3">
          <div class="card overflow-hidden border border-blue-300 p-2 bg-gradient-to-b from-spnr-blue-50 to-white">
            <h3 class="text-sm font-semibold text-gray-800 mb-2">Shop Info</h3>
            <div class="text-xs text-gray-600 space-y-3">
              <div class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-spnr-blue-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div>
                  <div class="font-medium text-gray-700">Address</div>
                  <div class="truncate">{{ shop.address }}</div>
                </div>
              </div>

              <div class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-spnr-blue-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <div>
                  <div class="font-medium text-gray-700">Contact</div>
                  <div>{{ shop.contact_number }}</div>
                </div>
              </div>
            </div>
          </div>

          <div class="card overflow-hidden border border-blue-300 p-2 bg-gradient-to-b from-spnr-blue-50 to-white">
            <h3 class="text-sm font-semibold text-gray-800 mb-2">Owner</h3>
            <div class="text-xs text-gray-600 space-y-2" v-if="shop.owner">
              <div class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-spnr-blue-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <div>
                  <div class="font-medium text-gray-700">Name</div>
                  <div class="truncate">{{ shop.owner.name }}</div>
                </div>
              </div>

              <div class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-spnr-blue-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <div>
                  <div class="font-medium text-gray-700">Email</div>
                  <div class="truncate text-xs text-gray-500">{{ shop.owner.email }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- View Services Button -->
        <div class="card bg-gradient-to-r from-spnr-blue-50 to-spnr-blue-100 border-2 border-spnr-blue-200">
        <!-- Keep text left and button inline at right across breakpoints -->
        <div class="flex items-center justify-between gap-4">
          <div class="flex-1 min-w-0">
            <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-1 truncate">Ready to place an order?</h3>
            <p class="text-sm lg:text-base text-gray-600 ">Browse available laundry services from this shop</p>
          </div>
          <button 
            @click="router.push(`/customer/shops/${shop.id}/services`)"
            :class="[
              'btn-primary whitespace-nowrap flex-shrink-0 ml-4',
              isMobileTwoColumn ? 'py-1.5 px-2 text-sm' : 'py-2 px-3'
            ]"
          >
            View Services
          </button>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-12">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <p class="text-gray-600">Shop not found or not available</p>
      <button @click="router.push('/customer/shops')" class="mt-4 btn-primary">
        Back to Shops
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const route = useRoute()
const loading = ref(true)
const shop = ref(null)
const isMobileTwoColumn = ref(false)
const isNarrowMobile = ref(false)

const updateMobileTwoColumn = () => {
  // treat tablet and mobile as compact two-column (width < 1024)
  isMobileTwoColumn.value = window.innerWidth < 1024
  // narrow mobile for full-width button behavior (width < 640)
  isNarrowMobile.value = window.innerWidth < 640
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}

const openDocument = (url) => {
  window.open(url, '_blank')
}

const fetchShopDetails = async () => {
  try {
    const shopId = route.params.id
    const response = await api.get(`/customer/shops/${shopId}`)
    shop.value = response.data
  } catch (error) {
    console.error('Error fetching shop details:', error)
    shop.value = null
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchShopDetails()
  updateMobileTwoColumn()
  window.addEventListener('resize', updateMobileTwoColumn)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateMobileTwoColumn)
})
</script>
