<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-50">
    <div class="text-center">
      <!-- SpnrCo Logo -->
      <div class="mb-6">
        <img src="/logo.png" alt="SpnrCo Logo" class="h-16 w-16 mx-auto rounded-full shadow-lg" />
      </div>

      <!-- Loading Spinner -->
      <div class="relative">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-spnr-blue-900 mx-auto mb-4"></div>
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="h-12 w-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full opacity-50"></div>
        </div>
      </div>

      <!-- Status Text -->
      <h2 class="text-xl font-semibold text-gray-800 mb-2">Completing authentication...</h2>
      <p class="text-sm text-gray-600">Please wait while we log you in</p>

      <!-- Progress Indicator -->
      <div class="mt-6 w-64 mx-auto">
        <div class="h-1 bg-gray-200 rounded-full overflow-hidden">
          <div class="h-full bg-gradient-to-r from-spnr-blue-900 to-blue-600 animate-progress"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

onMounted(async () => {
  const token = route.query.token
  const role = route.query.role
  const error = route.query.error

  if (error) {
    // Redirect to login with error message
    router.push({ path: '/login', query: { error: decodeURIComponent(error) } })
    return
  }

  if (token) {
    try {
      // Store the token
      localStorage.setItem('auth_token', token)
      authStore.token = token

      // Fetch user data
      await authStore.fetchUser()
      
      // Small delay for better UX
      await new Promise(resolve => setTimeout(resolve, 500))

      // Redirect based on role
      if (role === 'customer') {
        router.push('/customer/dashboard')
      } else if (role === 'owner') {
        router.push('/shop/dashboard')
      } else if (role === 'admin') {
        router.push('/admin/dashboard')
      } else {
        router.push('/')
      }
    } catch (err) {
      console.error('Auth callback error:', err)
      router.push({ path: '/login', query: { error: 'Authentication failed. Please try again.' } })
    }
  } else {
    router.push({ path: '/login', query: { error: 'No authentication token received.' } })
  }
})
</script>

<style scoped>
@keyframes progress {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.animate-progress {
  animation: progress 1.5s ease-in-out infinite;
}
</style>
