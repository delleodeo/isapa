<template>
  <div>
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8">Change Password</h1>

    <!-- Success Message -->
    <div v-if="passwordSuccess" class="max-w-2xl mx-auto mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between text-sm sm:text-base">
      <span>{{ passwordSuccess }}</span>
      <button @click="passwordSuccess = ''" class="text-green-700 hover:text-green-900">&times;</button>
    </div>

    <div class="max-w-2xl mx-auto">
      <div class="card overflow-hidden border border-blue-300">
        <div class="flex items-center mb-4">
          <svg class="w-6 h-6 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          <h2 class="text-xl font-semibold text-gray-800">Update Password</h2>
        </div>
        <form @submit.prevent="updatePassword" class="space-y-4">
          <div>
            <label class="label">Current Password <span class="text-red-500">*</span></label>
            <input v-model="passwordForm.current_password" type="password" required class="input-field" placeholder="Enter current password" />
          </div>
          <div>
            <label class="label">New Password <span class="text-red-500">*</span></label>
            <input v-model="passwordForm.new_password" type="password" required class="input-field" placeholder="Enter new password (min. 8 characters)" />
          </div>
          <div>
            <label class="label">Confirm New Password <span class="text-red-500">*</span></label>
            <input v-model="passwordForm.new_password_confirmation" type="password" required class="input-field" placeholder="Confirm new password" />
          </div>
          <div v-if="passwordError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ passwordError }}
          </div>
          <button type="submit" :disabled="passwordSubmitting" class="btn-primary disabled:opacity-50">
            {{ passwordSubmitting ? 'Updating...' : 'Update Password' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '../../services/api'

const passwordForm = ref({ current_password: '', new_password: '', new_password_confirmation: '' })
const passwordSubmitting = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

const updatePassword = async () => {
  passwordSubmitting.value = true
  passwordError.value = ''
  passwordSuccess.value = ''
  
  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    passwordError.value = 'New passwords do not match'
    passwordSubmitting.value = false
    return
  }
  
  if (passwordForm.value.new_password.length < 8) {
    passwordError.value = 'New password must be at least 8 characters'
    passwordSubmitting.value = false
    return
  }
  
  try {
    await api.put('/admin/update-password', {
      current_password: passwordForm.value.current_password,
      new_password: passwordForm.value.new_password,
      new_password_confirmation: passwordForm.value.new_password_confirmation
    })
    passwordSuccess.value = 'Password updated successfully!'
    passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' }
    setTimeout(() => passwordSuccess.value = '', 5000)
  } catch (err) {
    passwordError.value = err.response?.data?.message || 'Failed to update password'
  } finally {
    passwordSubmitting.value = false
  }
}
</script>
