<template>
  <div class="min-h-screen">
    <!-- Navbar -->
    <nav class="navbar sticky top-0 z-50">
      <div class="max-w-auto mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <div class="flex items-center group cursor-pointer" @click="$router.push('/')">
            <img src="/logo.png" alt="SpnrCo Logo" class="h-12 w-12 mr-3 rounded-full transition-transform duration-300 group-hover:scale-110" />
            <h1 class="text-2xl font-bold text-gradient">SpnrCo</h1>
          </div>
          
          <div class="hidden md:flex items-center space-x-8">
            <router-link to="/" class="navbar-link">Back to Home</router-link>
            <router-link to="/login" class="navbar-link">Login</router-link>
            <router-link to="/register" class="btn-primary">Book Now</router-link>
          </div>

          <!-- Mobile menu button -->
          <div class="md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="navbar-link p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu (moved inside navbar to inherit navbar background) -->
        <div v-if="mobileMenuOpen" class="md:hidden border-t py-4 mobile-menu">
          <router-link to="/" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link hover:bg-spnr-blue-50">Back to Home</router-link>
          <router-link to="/login" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link hover:bg-spnr-blue-50">Login</router-link>
          <router-link to="/register" @click="mobileMenuOpen = false" class="block mx-4 my-2 btn-primary text-center">Book Now</router-link>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
      <!-- Header -->
      <div class="mb-8">
        <button @click="$router.back()" class="flex items-center text-spnr-blue-900 hover:text-spnr-blue-700 mb-4">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Back
        </button>
        <h1 v-if="category" class="text-4xl font-bold text-gray-900 mb-2">{{ category.name }}</h1>
        <p v-if="category" class="text-lg text-gray-600">{{ category.description || 'Browse all available services' }}</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
        <p class="mt-4 text-gray-600">Loading services...</p>
      </div>

      <!-- Services by Shop -->
      <div v-else-if="shopServices.length > 0" class="space-y-8">
        <div v-for="shopData in shopServices" :key="shopData.shop.id" class="card border border-blue-300">
          <!-- Shop Header -->
          <div class="mb-6">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <h2 class="text-2xl font-bold text-gray-900">{{ shopData.shop.shop_name }}</h2>
                  <span 
                    v-if="shopData.shop.verification_status === 'approved'" 
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    title="Verified Shop">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Verified
                  </span>
                </div>
                <p class="text-gray-600 text-sm mb-2">{{ shopData.shop.description }}</p>
                <div class="flex items-center text-sm text-gray-500 gap-4">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ shopData.shop.address }}
                  </div>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ shopData.shop.contact_number }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Services Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="service in shopData.services" :key="service.id" class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-spnr-blue-300 transition-colors">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ service.service_name }}</h3>
              <p class="text-sm text-gray-600 mb-3">{{ service.description }}</p>
              <div class="flex items-center justify-between">
                <div class="text-2xl font-bold text-spnr-blue-900">
                  ₱{{ service.price }}
                  <span class="text-sm font-normal text-gray-600">/kg</span>
                </div>
                <router-link 
                  :to="`/customer/shops/${shopData.shop.id}/services`" 
                  class="btn-primary text-sm px-4 py-2">
                  Book Now
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <p class="text-gray-600">No services available in this category yet</p>
        <router-link to="/" class="mt-4 inline-block btn-primary">
          Back to Home
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const loading = ref(true)
const category = ref(null)
const shopServices = ref([])
const mobileMenuOpen = ref(false)

const fetchCategoryServices = async () => {
  try {
    const categoryId = route.params.categoryId
    
    // Fetch category details
    const categoryResponse = await api.get(`/categories`)
    category.value = categoryResponse.data.find(cat => cat.id == categoryId)
    
    // Fetch all shops
    const shopsResponse = await api.get('/shops')
    const shops = shopsResponse.data
    
    // For each shop, fetch services and filter by category
    const servicesPromises = shops.map(async (shop) => {
      try {
        const servicesResponse = await api.get(`/services/${shop.id}`)
        const categoryServices = servicesResponse.data.filter(service => service.category_id == categoryId)
        
        if (categoryServices.length > 0) {
          return {
            shop: shop,
            services: categoryServices
          }
        }
        return null
      } catch (error) {
        return null
      }
    })
    
    const results = await Promise.all(servicesPromises)
    shopServices.value = results.filter(result => result !== null)
    
  } catch (error) {
    console.error('Error fetching category services:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCategoryServices()
})
</script>
