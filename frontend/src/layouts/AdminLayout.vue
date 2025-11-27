<template>
  <div :class="['min-h-screen', { 'admin-compact': compactMobile }]">
    <!-- Navigation -->
    <nav class="navbar sticky top-0 z-50">
      <div class="w-full px-6 lg:px-12 xl:px-16">
        <div class="flex justify-between items-center h-20">
          <div class="flex items-center group">
            <img src="/logo.png" alt="SpnrCo Logo" class="h-10 w-10 sm:h-12 sm:w-12 mr-2 sm:mr-3 rounded-full transition-transform duration-300 group-hover:scale-110" />
            <h1 class="text-xl sm:text-2xl font-bold text-gradient">SpnrCo</h1>
          </div>
          
          <!-- Desktop Navigation -->
          <div class="hidden lg:flex items-center space-x-3 xl:space-x-4">
            <router-link
              to="/admin/dashboard"
              class="nav-btn"
              active-class="nav-btn-active"
            >
              Dashboard
            </router-link>
            <router-link
              to="/admin/users"
              class="nav-btn"
              active-class="nav-btn-active"
            >
              Users
            </router-link>
            <router-link
              to="/admin/categories"
              class="nav-btn"
              active-class="nav-btn-active"
            >
              Categories
            </router-link>
            <router-link
              to="/admin/shops"
              class="nav-btn"
              active-class="nav-btn-active"
            >
              Shops
            </router-link>
            <router-link
              to="/admin/orders"
              class="nav-btn"
              active-class="nav-btn-active"
            >
              Orders
            </router-link>
            
            <div class="border-l border-blue-300 pl-4 ml-4 flex items-center space-x-3">
              <!-- Admin Settings Dropdown -->
              <div class="relative">
                <button
                  @click="adminSettingsOpen = !adminSettingsOpen"
                  class="nav-btn flex items-center"
                  :class="{ 'nav-btn-active': $route.path.startsWith('/admin/change-') }"
                >
                  {{ authStore.user?.name }}
                  <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': adminSettingsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
                <div
                  v-if="adminSettingsOpen"
                  class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                  @click="adminSettingsOpen = false"
                >
                  <router-link
                    to="/admin/change-email"
                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 transition-colors first:rounded-t-lg"
                  >
                    Change Email
                  </router-link>
                  <router-link
                    to="/admin/change-password"
                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 transition-colors border-t border-gray-100 last:rounded-b-lg"
                  >
                    Change Password
                  </router-link>
                </div>
              </div>

              <button
                @click="handleLogout"
                class="logout-btn flex items-center"
              >
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
              </button>
            </div>
          </div>

          <!-- Mobile Menu Button -->
          <div class="lg:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-spnr-blue-900 p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="mobile-menu lg:hidden border-t border-blue-200 py-2">
          <router-link to="/admin/dashboard" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Dashboard</router-link>
          <router-link to="/admin/users" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Users</router-link>
          <router-link to="/admin/categories" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Categories</router-link>
          <router-link to="/admin/shops" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Shops</router-link>
          <router-link to="/admin/orders" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Orders</router-link>
          <div class="border-t border-blue-200 mt-2 pt-2">
            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">{{ authStore.user?.name }}</p>
            <router-link to="/admin/change-email" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Change Email</router-link>
            <router-link to="/admin/change-password" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Change Password</router-link>
          </div>
          <div class="border-t border-blue-200 mt-2 pt-2">
            <button @click="handleLogout" class="block w-full rounded text-left px-4 py-3 text-sm font-semibold text-red-800 hover:text-white hover:bg-red-800 hover:rounded-lg transition-all flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useCompact } from '../composables/useCompact'

const router = useRouter()
const authStore = useAuthStore()
const mobileMenuOpen = ref(false)
const adminSettingsOpen = ref(false)

const { compactMobile } = useCompact(1024)

const handleLogout = async () => {
  if (!confirm('Are you sure you want to logout?')) return
  
  await authStore.logout()
  router.push('/')
}
</script>
