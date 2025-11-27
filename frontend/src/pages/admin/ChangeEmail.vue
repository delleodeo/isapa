<template>
  <div>
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8">Change Email</h1>

    <!-- Success Message -->
    <div v-if="emailSuccess" class="max-w-2xl mx-auto mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between text-sm sm:text-base">
      <span>{{ emailSuccess }}</span>
      <button @click="emailSuccess = ''" class="text-green-700 hover:text-green-900">&times;</button>
    </div>

    <div class="max-w-2xl mx-auto">
      <div class="card overflow-hidden border border-blue-300">
        <div class="flex items-center mb-4">
          <svg class="w-6 h-6 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <h2 class="text-xl font-semibold text-gray-800">Update Email Address</h2>
        </div>
        <form @submit.prevent="updateEmail" class="space-y-4">
          <div>
            <label class="label">Current Email</label>
            <input :value="authStore.user?.email" disabled class="input-field bg-gray-100" />
          </div>
          <div>
            <label class="label">New Email <span class="text-red-500">*</span></label>
            <input v-model="emailForm.email" type="email" required class="input-field" placeholder="Enter new email address" />
          </div>
          <div>
            <label class="label">Confirm Password <span class="text-red-500">*</span></label>
            <input v-model="emailForm.password" type="password" required class="input-field" placeholder="Enter your password to confirm" />
          </div>
          <div v-if="emailError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ emailError }}
          </div>
          <button type="submit" :disabled="emailSubmitting" class="btn-primary disabled:opacity-50">
            {{ emailSubmitting ? 'Updating...' : 'Update Email' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const authStore = useAuthStore()

const emailForm = ref({ email: '', password: '' })
const emailSubmitting = ref(false)
const emailError = ref('')
const emailSuccess = ref('')

const updateEmail = async () => {
  emailSubmitting.value = true
  emailError.value = ''
  emailSuccess.value = ''
  
  try {
    const response = await api.put('/admin/update-email', emailForm.value)
    
    // Update auth store with new email
    if (response.data.user) {
      authStore.user = response.data.user
      localStorage.setItem('user', JSON.stringify(response.data.user))
    }
    
    emailSuccess.value = 'Email updated successfully!'
    emailForm.value = { email: '', password: '' }
    setTimeout(() => emailSuccess.value = '', 5000)
  } catch (error) {
    emailError.value = error.response?.data?.message || 'Failed to update email'
  } finally {
    emailSubmitting.value = false
  }
}
</script>
