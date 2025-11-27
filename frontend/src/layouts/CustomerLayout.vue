<template>
  <div class="min-h-screen">
    <!-- Navigation -->
    <nav class="navbar sticky top-0 z-50">
      <div class="w-full px-6 lg:px-12 xl:px-16">
        <div class="flex justify-between items-center h-20">
          <div class="flex items-center group">
            <img src="/logo.png" alt="SpnrCo Logo" class="h-10 w-10 sm:h-12 sm:w-12 mr-2 sm:mr-3 rounded-full transition-transform duration-300 group-hover:scale-110" />
            <h1 class="text-xl sm:text-2xl font-bold text-gradient">SpnrCo</h1>
          </div>
          
          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center space-x-3 xl:space-x-4">
            <router-link
              to="/customer/dashboard"
              class="nav-btn"
              active-class="nav-btn-active"
            >
              Dashboard
            </router-link>
            <router-link
              to="/customer/shops"
              class="nav-btn"
              active-class="nav-btn-active"
            >
              Browse Shops
            </router-link>
            <router-link
              to="/customer/services"
              class="nav-btn"
              active-class="nav-btn-active"
            >
              Services
            </router-link>
            <router-link
              to="/customer/orders"
              class="nav-btn"
              active-class="nav-btn-active"
            >
              My Orders
            </router-link>
            <router-link
              to="/customer/track"
              class="nav-btn"
              active-class="nav-btn-active"
            >
              Track Order
            </router-link>
            
            <div class="border-l border-blue-300 pl-4 ml-4 flex items-center space-x-3">
              <!-- Notification Bell -->
              <button
                @click="toggleNotifications"
                class="relative p-2 text-gray-700 hover:text-spnr-blue-900 rounded-full hover:bg-white/50 transition-all duration-300"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span
                  v-if="unreadCount > 0"
                  class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
                >
                  {{ unreadCount > 9 ? '9+' : unreadCount }}
                </span>
              </button>

              <!-- Profile Settings Dropdown -->
              <div class="relative">
                <button
                  @click="profileSettingsOpen = !profileSettingsOpen"
                  class="nav-btn flex items-center"
                  :class="{ 'nav-btn-active': $route.path.startsWith('/customer/profile') || $route.path.startsWith('/customer/security') }"
                >
                  {{ authStore.user?.name }}
                  <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': profileSettingsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
                <div
                  v-if="profileSettingsOpen"
                  class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                  @click="profileSettingsOpen = false"
                >
                  <router-link
                    to="/customer/profile"
                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 transition-colors first:rounded-t-lg"
                  >
                    Profile Information
                  </router-link>
                  <router-link
                    to="/customer/security"
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
          <div class="md:hidden flex items-center space-x-2">
            <!-- Notification Bell (Mobile) -->
            <div class="relative">
              <button
                @click="toggleNotifications"
                class="relative p-2 text-gray-700 hover:text-spnr-blue-900 rounded-full hover:bg-spnr-blue-50"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span
                  v-if="unreadCount > 0"
                  class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
                >
                  {{ unreadCount > 9 ? '9+' : unreadCount }}
                </span>
              </button>
            </div>

            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-spnr-blue-900 p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="mobile-menu md:hidden border-t py-4 mobile-menu">
          <router-link to="/customer/dashboard" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Dashboard</router-link>
          <router-link to="/customer/shops" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Browse Shops</router-link>
          <router-link to="/customer/services" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Services</router-link>
          <router-link to="/customer/orders" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">My Orders</router-link>
          <router-link to="/customer/track" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Track Order</router-link>
          <div class="border-t border-blue-200 mt-2 pt-2">
            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">{{ authStore.user?.name }}</p>
            <router-link to="/customer/profile" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Profile Information</router-link>
            <router-link to="/customer/security" @click="mobileMenuOpen = false" class="block px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 hover:bg-spnr-blue-50 hover:text-spnr-blue-900 font-medium transition-all">Change Password</router-link>
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

    <!-- Notifications Dropdown (Positioned globally for mobile and desktop) -->
    <Teleport to="body">
      <div
        v-if="showNotifications"
        class="fixed inset-x-0 top-20 mx-auto lg:right-4 xl:right-8 lg:left-auto lg:top-24 w-full sm:w-[90%] lg:w-96 bg-white rounded-none sm:rounded-lg lg:rounded-lg shadow-2xl border-t lg:border border-gray-200 z-50 flex flex-col"
        style="max-height: calc(100vh - 5rem);"
        @click.stop
      >
        <div class="p-3 sm:p-4 border-b border-gray-200 bg-white flex-shrink-0">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800">Notifications</h3>
            <button
              @click="closeNotifications"
              class="lg:hidden text-gray-400 hover:text-gray-600 p-1"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <div class="flex gap-2">
            <button
              v-if="notifications.length > 0"
              @click="markAllAsRead"
              class="text-xs sm:text-sm text-spnr-blue-900 hover:text-spnr-blue-700 font-medium px-2 py-1 rounded hover:bg-spnr-blue-50 transition-colors"
            >
              Mark all read
            </button>
            <button
              v-if="notifications.length > 0"
              @click="clearAllNotifications"
              class="text-xs sm:text-sm text-red-600 hover:text-red-700 font-medium px-2 py-1 rounded hover:bg-red-50 transition-colors"
            >
              Clear all
            </button>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto">
          <div v-if="loadingNotifications" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-spnr-blue-900"></div>
          </div>

          <div v-else-if="notifications.length === 0" class="p-6 sm:p-8 text-center text-gray-500">
            <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-sm">No notifications yet</p>
          </div>

          <div v-else>
            <div
              v-for="notification in displayedNotifications"
              :key="notification.id"
              class="relative group"
            >
              <div
                @click="markAsRead(notification.id)"
                :class="[
                  'p-3 sm:p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors',
                  !notification.is_read ? 'bg-spnr-blue-50' : ''
                ]"
              >
                <div class="flex items-start gap-2 sm:gap-3">
                  <div
                    :class="[
                      'w-2 h-2 rounded-full mt-1.5 sm:mt-2 flex-shrink-0',
                      !notification.is_read ? 'bg-spnr-blue-900' : 'bg-gray-300'
                    ]"
                  ></div>
                  <div class="flex-1 min-w-0 pr-6">
                    <p class="text-xs sm:text-sm text-gray-800 break-words leading-relaxed">{{ notification.message }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ formatDate(notification.created_at) }}</p>
                  </div>
                  <button
                    @click.stop="deleteNotification(notification.id)"
                    class="absolute right-2 top-3 opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-600 p-1 transition-opacity"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="notifications.length > 0" class="border-t border-gray-200 bg-white flex-shrink-0">
          <button
            v-if="!showAllNotifications && notifications.length > 5"
            @click="showAllNotifications = true"
            class="w-full p-3 text-xs sm:text-sm text-spnr-blue-900 hover:text-spnr-blue-700 font-medium hover:bg-spnr-blue-50 transition-colors"
          >
            See More ({{ notifications.length - 5 }} more)
          </button>
          <button
            v-if="showAllNotifications && notifications.length > 5"
            @click="showAllNotifications = false"
            class="w-full p-3 text-xs sm:text-sm text-spnr-blue-900 hover:text-spnr-blue-700 font-medium hover:bg-spnr-blue-50 transition-colors border-t border-gray-100"
          >
            Show Less
          </button>
          <button
            @click="closeNotifications"
            class="hidden lg:block w-full p-3 text-xs sm:text-sm text-gray-600 hover:text-gray-800 font-medium hover:bg-gray-50 transition-colors border-t border-gray-100"
          >
            Close
          </button>
        </div>
      </div>
    </Teleport>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const router = useRouter()
const authStore = useAuthStore()
const showNotifications = ref(false)
const profileSettingsOpen = ref(false)
const loadingNotifications = ref(false)
const notifications = ref([])
const mobileMenuOpen = ref(false)
const showAllNotifications = ref(false)

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.is_read).length
})

const displayedNotifications = computed(() => {
  if (showAllNotifications.value) {
    return notifications.value
  }
  return notifications.value.slice(0, 5)
})

const fetchNotifications = async () => {
  loadingNotifications.value = true
  try {
    const response = await api.get('/customer/notifications')
    notifications.value = response.data
  } catch (error) {
    console.error('Error fetching notifications:', error)
  } finally {
    loadingNotifications.value = false
  }
}

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  if (showNotifications.value && notifications.value.length === 0) {
    fetchNotifications()
  }
}

const closeNotifications = () => {
  showNotifications.value = false
}

const markAsRead = async (id) => {
  try {
    await api.put(`/customer/notifications/${id}/read`)
    const notification = notifications.value.find(n => n.id === id)
    if (notification) {
      notification.is_read = true
    }
  } catch (error) {
    console.error('Error marking notification as read:', error)
  }
}

const markAllAsRead = async () => {
  const unread = notifications.value.filter(n => !n.is_read)
  try {
    // Mark all as read in parallel for faster performance
    await Promise.all(
      unread.map(notification => 
        api.put(`/customer/notifications/${notification.id}/read`)
      )
    )
    // Update local state
    unread.forEach(notification => {
      notification.is_read = true
    })
  } catch (error) {
    console.error('Error marking all as read:', error)
  }
}

const deleteNotification = async (id) => {
  try {
    await api.delete(`/customer/notifications/${id}`)
    notifications.value = notifications.value.filter(n => n.id !== id)
  } catch (error) {
    console.error('Error deleting notification:', error)
  }
}

const clearAllNotifications = async () => {
  if (!confirm('Are you sure you want to clear all notifications?')) return
  
  try {
    // Delete all notifications in parallel for faster performance
    await Promise.all(
      notifications.value.map(notification => 
        api.delete(`/customer/notifications/${notification.id}`)
      )
    )
    notifications.value = []
    showAllNotifications.value = false
  } catch (error) {
    console.error('Error clearing notifications:', error)
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  return date.toLocaleDateString()
}

const handleLogout = async () => {
  if (!confirm('Are you sure you want to logout?')) return
  
  await authStore.logout()
  router.push('/')
}

// Fetch notifications on mount and periodically
onMounted(() => {
  fetchNotifications()
  // Refresh notifications every 30 seconds
  setInterval(fetchNotifications, 30000)
})
</script>
