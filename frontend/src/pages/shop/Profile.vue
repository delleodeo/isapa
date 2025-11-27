<template>
  <div>
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8">Owner Profile</h1>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
    </div>

    <div v-else class="max-w-2xl mx-auto">
      <div class="card overflow-hidden border border-blue-300">
      <div class="flex items-center mb-4">
        <svg class="w-6 h-6 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
      </div>
      
      <form @submit.prevent="updateProfile" class="space-y-4">
        <div>
          <label class="label">Full Name <span class="text-red-500">*</span></label>
          <input v-model="profileForm.name" required class="input-field" placeholder="Enter your full name" />
        </div>
        
        <div>
          <label class="label">Email</label>
          <input :value="authStore.user?.email" disabled class="input-field bg-gray-100" />
          <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
        </div>
        
        <div>
          <label class="label">Phone Number</label>
          <input v-model="profileForm.phone" type="tel" class="input-field" placeholder="e.g., +63 912 345 6789" />
        </div>
        
        <div>
          <label class="label">Personal Address</label>
          <textarea v-model="profileForm.address" rows="3" class="input-field" placeholder="Enter your personal address"></textarea>
        </div>
        
        <div v-if="profileError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
          {{ profileError }}
        </div>
        
        <div v-if="profileSuccess" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
          {{ profileSuccess }}
        </div>
        
        <button type="submit" :disabled="profileSubmitting" class="btn-primary disabled:opacity-50 w-full">
          {{ profileSubmitting ? 'Updating...' : 'Update Profile' }}
        </button>
      </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const authStore = useAuthStore()
const loading = ref(true)
const profileSubmitting = ref(false)
const profileError = ref('')
const profileSuccess = ref('')

const profileForm = ref({
  name: '',
  phone: '',
  address: ''
})

const fetchProfile = async () => {
  try {
    const response = await api.get('/shop/profile')
    profileForm.value = {
      name: response.data.name || '',
      phone: response.data.phone || '',
      address: response.data.address || ''
    }
  } catch (error) {
    console.error('Error fetching profile:', error)
    profileError.value = 'Failed to load profile information'
  } finally {
    loading.value = false
  }
}

const updateProfile = async () => {
  profileSubmitting.value = true
  profileError.value = ''
  profileSuccess.value = ''
  
  try {
    const response = await api.put('/shop/profile/update', profileForm.value)
    
    // Update auth store with new user data
    await authStore.fetchUser()
    
    profileSuccess.value = 'Profile updated successfully!'
    setTimeout(() => {
      profileSuccess.value = ''
    }, 3000)
  } catch (error) {
    console.error('Error updating profile:', error)
    profileError.value = error.response?.data?.message || 'Failed to update profile'
  } finally {
    profileSubmitting.value = false
  }
}

onMounted(() => {
  fetchProfile()
})
</script>