<template>
  <div class="min-h-screen">
    <!-- Navigation Bar -->
    <nav class="navbar sticky top-0 z-50">
      <div class="max-w-auto mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <div class="flex items-center group">
            <router-link to="/" class="flex items-center">
              <img src="/logo.png" alt="SpnrCo Logo" class="h-12 w-12 mr-3 rounded-full transition-transform duration-300 group-hover:scale-110" />
              <h1 class="text-2xl font-bold text-gradient">SpnrCo</h1>
            </router-link>
          </div>
          
          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center space-x-8">
            <router-link to="/" class="navbar-link">Home</router-link>
            <a href="/#shops" class="navbar-link">Laundry Shops</a>
            <a href="/#services" class="navbar-link">Services</a>
            <a href="/#contact" class="navbar-link">Contact</a>
          </div>

          <!-- Desktop Buttons -->
          <div class="hidden md:flex items-center space-x-4">
            <router-link to="/login" class="navbar-link">Login</router-link>
            <router-link to="/register" class="btn-primary">Book Now</router-link>
          </div>

          <!-- Mobile Menu Button -->
          <div class="md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="navbar-link p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="md:hidden border-t py-4 mobile-menu">
          <router-link to="/" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link">Home</router-link>
          <a href="/#shops" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link">Laundry Shops</a>
          <a href="/#services" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link">Services</a>
          <a href="/#contact" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link">Contact</a>
          <router-link to="/login" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link">Login</router-link>
          <router-link to="/register" @click="mobileMenuOpen = false" class="block mx-4 my-2 btn-primary text-center">Book Now</router-link>
        </div>
      </div>
    </nav>

    <!-- Register Form -->
    <div :class="isCompact ? 'flex items-center justify-center px-3 py-6' : 'flex items-center justify-center px-4 py-8 sm:py-12'">
      <div class="w-full max-w-md">
      <!-- Register Card -->
      <div :class="isCompact ? 'bg-white rounded-xl shadow-lg p-6' : 'card'">
        <div :class="isCompact ? 'text-center mb-4' : 'text-center mb-6 sm:mb-8'">
          <div :class="isCompact ? 'flex items-center justify-center mb-2' : 'mb-2'">
            <img v-if="isCompact" src="/logo.png" alt="SpnrCo Logo" class="h-10 w-10 mr-2 rounded-full" />
            <h1 :class="isCompact ? 'text-2xl font-bold text-spnr-blue-900' : 'text-3xl sm:text-4xl font-bold text-spnr-blue-900'">SpnrCo</h1>
          </div>
          <p :class="isCompact ? 'text-xs text-gray-600' : 'text-sm sm:text-base text-gray-600'">Create your account</p>
        </div>

        <form @submit.prevent="handleRegister" :class="isCompact ? 'space-y-3.5' : 'space-y-5'">
          <!-- Name Field -->
          <div>
            <label for="name" :class="isCompact ? 'block text-xs font-medium text-gray-700 mb-1' : 'label'">Full Name</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              :class="isCompact ? 'w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-300 focus:border-spnr-blue-900 transition-colors' : 'input-field'"
              placeholder="Enter your full name"
            />
          </div>

          <!-- Email Field -->
          <div>
            <label for="email" :class="isCompact ? 'block text-xs font-medium text-gray-700 mb-1' : 'label'">Email Address</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              :class="isCompact ? 'w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-300 focus:border-spnr-blue-900 transition-colors' : 'input-field'"
              placeholder="Enter your email"
            />
          </div>

          <!-- Phone Field -->
          <div>
            <label for="phone" :class="isCompact ? 'block text-xs font-medium text-gray-700 mb-1' : 'label'">Phone Number</label>
            <input
              id="phone"
              v-model="form.phone"
              type="tel"
              :class="isCompact ? 'w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-300 focus:border-spnr-blue-900 transition-colors' : 'input-field'"
              placeholder="Enter your phone number"
            />
          </div>

          <!-- Role Selection -->
          <div>
            <label for="role" :class="isCompact ? 'block text-xs font-medium text-gray-700 mb-1' : 'label'">Register as</label>
            <select
              id="role"
              v-model="form.role"
              required
              :class="isCompact ? 'w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-300 focus:border-spnr-blue-900 transition-colors' : 'input-field'"
            >
              <option value="">Select role</option>
              <option value="customer">Customer</option>
              <option value="owner">Laundry Shop Owner</option>
            </select>
            <p v-if="form.role === 'owner'" :class="isCompact ? 'text-xs text-blue-600 mt-0.5' : 'text-xs text-blue-600 mt-1'">
              ℹ️ You can set up your shop profile and submit verification documents after registration.
            </p>
          </div>

          <!-- Password Field -->
          <div>
            <label for="password" :class="isCompact ? 'block text-xs font-medium text-gray-700 mb-1' : 'label'">Password</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              minlength="8"
              :class="isCompact ? 'w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-300 focus:border-spnr-blue-900 transition-colors' : 'input-field'"
              placeholder="Enter your password (min 8 characters)"
            />
          </div>

          <!-- Confirm Password Field -->
          <div>
            <label for="password_confirmation" :class="isCompact ? 'block text-xs font-medium text-gray-700 mb-1' : 'label'">Confirm Password</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              :class="isCompact ? 'w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-spnr-blue-300 focus:border-spnr-blue-900 transition-colors' : 'input-field'"
              placeholder="Re-enter your password"
            />
          </div>

          <!-- Error Message -->
          <div v-if="error" :class="isCompact ? 'bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-xs' : 'bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm'">
            {{ error }}
          </div>

          <!-- Register Button -->
          <button
            type="submit"
            :disabled="loading"
            :class="isCompact ? 'w-full bg-spnr-blue-900 text-white py-2.5 px-4 rounded-lg hover:bg-spnr-blue-700 transition-all duration-300 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed' : 'w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed'"
          >
            {{ loading ? 'Creating account...' : 'Register' }}
          </button>
        </form>

        <!-- Social Registration -->
        <div :class="isCompact ? 'mt-4' : 'mt-5'">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center" :class="isCompact ? 'text-xs' : 'text-sm'">
              <span class="px-2 bg-white text-gray-500">Or register with</span>
            </div>
          </div>

          <div :class="isCompact ? 'mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2.5' : 'mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3'">
            <button @click="handleGoogleRegister" type="button" :class="isCompact ? 'w-full inline-flex justify-center items-center gap-2 py-2 px-3 border border-gray-300 rounded-lg shadow-sm bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors' : 'w-full inline-flex justify-center items-center gap-2 py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors'">
              <svg :class="isCompact ? 'w-4 h-4' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
              </svg>
              <span v-if="!isCompact">Google</span>
            </button>
            <button @click="handleFacebookRegister" type="button" :class="isCompact ? 'w-full inline-flex justify-center items-center gap-2 py-2 px-3 border border-gray-300 rounded-lg shadow-sm bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors' : 'w-full inline-flex justify-center items-center gap-2 py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors'">
              <svg :class="isCompact ? 'w-4 h-4 text-blue-600' : 'w-5 h-5 text-blue-600'" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              <span v-if="!isCompact">Facebook</span>
            </button>
          </div>
        </div>

        <!-- Login Link -->
        <div :class="isCompact ? 'mt-4 text-center text-xs' : 'mt-6 text-center text-sm'">
          <span class="text-gray-600">Already have an account? </span>
          <router-link to="/login" class="text-spnr-blue-900 hover:text-spnr-blue-700 font-medium">
            Sign in
          </router-link>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const mobileMenuOpen = ref(false)

// Responsive compact view
const isCompact = ref(typeof window !== 'undefined' ? window.innerWidth < 1024 : false)
const updateCompact = () => {
  if (typeof window !== 'undefined') isCompact.value = window.innerWidth < 1024
}

const form = ref({
  name: '',
  email: '',
  phone: '',
  role: '',
  password: '',
  password_confirmation: '',
})

const loading = ref(false)
const error = ref('')

const handleRegister = async () => {
  // Validate passwords match
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Passwords do not match'
    return
  }

  loading.value = true
  error.value = ''

  try {
    await authStore.register(form.value)
    
    // Redirect based on role
    const role = authStore.getRole()
    if (role === 'customer') {
      router.push('/customer/dashboard')
    } else if (role === 'owner') {
      router.push('/shop/dashboard')
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Registration failed. Please try again.'
    if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = errors.join(', ')
    }
  } finally {
    loading.value = false
  }
}

// Social Registration Handlers
const handleGoogleRegister = () => {
  // Redirect to backend Google OAuth endpoint with registration intent
  window.location.href = `${import.meta.env.VITE_API_URL || 'http://localhost:8000'}/api/auth/google?register=true`
}

const handleFacebookRegister = () => {
  // Redirect to backend Facebook OAuth endpoint with registration intent
  window.location.href = `${import.meta.env.VITE_API_URL || 'http://localhost:8000'}/api/auth/facebook?register=true`
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('resize', updateCompact)
    updateCompact()
  }
})

onBeforeUnmount(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('resize', updateCompact)
  }
})
</script>
