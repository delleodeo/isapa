<template>
  <div>
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8">Security Settings</h1>

    <!-- Success Message -->
    <div v-if="success" class="max-w-2xl mx-auto mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between text-sm sm:text-base">
      <span>{{ success }}</span>
      <button @click="success = ''" class="text-green-700 hover:text-green-900">&times;</button>
    </div>

    <div class="max-w-2xl mx-auto">
      <!-- Desktop / Wide layout (unchanged) -->
      <div v-if="!isCompact">
        <!-- Change Password -->
        <div class="card overflow-hidden border border-blue-300">
          <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h2 class="text-xl font-semibold text-gray-800">Change Password</h2>
          </div>
          <form @submit.prevent="updatePassword" class="space-y-4">
            <!-- Show info for OAuth users -->
            <div v-if="isOAuthUser" class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm">
              <p class="font-medium">Setting a Password</p>
              <p class="mt-1">You logged in with Google/Facebook. Set a new password.</p>
            </div>
            
            <!-- Current password field - only for non-OAuth users -->
            <div v-if="!isOAuthUser">
              <label class="label">Current Password <span class="text-red-500">*</span></label>
              <input v-model="form.current_password" type="password" required class="input-field overflow-hidden border-gray-200" placeholder="Enter current password" />
            </div>
            <div>
              <label class="label">New Password <span class="text-red-500">*</span></label>
              <input v-model="form.new_password" type="password" required class="input-field overflow-hidden border-gray-200" placeholder="Enter new password (min. 8 characters)" />
            </div>
            <div>
              <label class="label">Confirm New Password <span class="text-red-500">*</span></label>
              <input v-model="form.new_password_confirmation" type="password" required class="input-field overflow-hidden border-gray-200" placeholder="Confirm new password" />
            </div>
            <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
              {{ error }}
            </div>
            <button type="submit" :disabled="submitting" class="btn-primary disabled:opacity-50">
              {{ submitting ? 'Updating...' : 'Update Password' }}
            </button>
          </form>
        </div>
      </div>

      <!-- Compact mobile/tablet layout: stacked, condensed form -->
      <div v-else>
        <div class="bg-white rounded-lg border border-blue-200 p-4">
          <div class="flex items-center mb-3">
            <svg class="w-5 h-5 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h2 class="text-lg font-semibold text-gray-800">Change Password</h2>
          </div>

          <form @submit.prevent="updatePassword" class="space-y-3">
            <!-- Show info for OAuth users -->
            <div v-if="isOAuthUser" class="bg-blue-50 border border-blue-200 text-blue-700 px-3 py-2 rounded text-sm">
              <p class="font-medium">Setting a Password</p>
              <p class="mt-1 text-xs">You logged in with Google/Facebook. Set a new password.</p>
            </div>
            
            <!-- Current password field - only for non-OAuth users -->
            <div v-if="!isOAuthUser">
              <label class="block text-sm font-medium text-gray-700">Current Password <span class="text-red-500">*</span></label>
              <input v-model="form.current_password" type="password" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md" placeholder="Enter current password" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">New Password <span class="text-red-500">*</span></label>
              <input v-model="form.new_password" type="password" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md" placeholder="Enter new password (min. 8 characters)" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Confirm New Password <span class="text-red-500">*</span></label>
              <input v-model="form.new_password_confirmation" type="password" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md" placeholder="Confirm new password" />
            </div>

            <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded text-sm">{{ error }}</div>

            <div class="flex justify-center">
              
              <button type="submit" :disabled="submitting" class="btn-primary w-1/2 px-3 py-2 text-sm bg-spnr-blue-600 text-white rounded-md">{{ submitting ? 'Updating...' : 'Update' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const authStore = useAuthStore()

const form = ref({ current_password: '', new_password: '', new_password_confirmation: '' })
const submitting = ref(false)
const error = ref('')
const success = ref('')

// responsive compact flag (mobile/tablet)
const isCompact = ref(false)

// Check if user is OAuth user (logged in via Google/Facebook)
const isOAuthUser = computed(() => {
  return authStore.user?.google_id || authStore.user?.facebook_id
})

const updateCompact = () => {
  isCompact.value = window.innerWidth < 1024
}

const updatePassword = async () => {
  submitting.value = true
  error.value = ''
  success.value = ''
  
  if (form.value.new_password !== form.value.new_password_confirmation) {
    error.value = 'New passwords do not match'
    submitting.value = false
    return
  }
  
  if (form.value.new_password.length < 8) {
    error.value = 'New password must be at least 8 characters'
    submitting.value = false
    return
  }
  
  try {
    const payload = {
      new_password: form.value.new_password,
      new_password_confirmation: form.value.new_password_confirmation
    }
    
    // Only include current_password if not OAuth user
    if (!isOAuthUser.value) {
      payload.current_password = form.value.current_password
    }
    
    await api.put('/customer/update-password', payload)
    success.value = 'Password updated successfully!'
    form.value = { current_password: '', new_password: '', new_password_confirmation: '' }
    setTimeout(() => success.value = '', 5000)
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update password'
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  updateCompact()
  window.addEventListener('resize', updateCompact)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateCompact)
})
</script>
