<template>
  <div>
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8">Admin Settings</h1>

    <!-- Success Messages -->
    <div v-if="emailSuccess" class="max-w-7xl mx-auto mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between text-sm sm:text-base">
      <span>{{ emailSuccess }}</span>
      <button @click="emailSuccess = ''" class="text-green-700 hover:text-green-900">&times;</button>
    </div>
    <div v-if="passwordSuccess" class="max-w-7xl mx-auto mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between text-sm sm:text-base">
      <span>{{ passwordSuccess }}</span>
      <button @click="passwordSuccess = ''" class="text-green-700 hover:text-green-900">&times;</button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-7xl mx-auto">
      <!-- Change Email -->
      <div class="card overflow-hidden border border-blue-300z">
        <div class="flex items-center mb-4">
          <svg class="w-6 h-6 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <h2 class="text-xl font-semibold text-gray-800">Change Email</h2>
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

      <!-- Change Password -->
      <div class="card overflow-hidden border border-blue-300">
        <div class="flex items-center mb-4">
          <svg class="w-6 h-6 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          <h2 class="text-xl font-semibold text-gray-800">Change Password</h2>
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
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const authStore = useAuthStore()

const emailForm = ref({ email: '', password: '' })
const passwordForm = ref({ current_password: '', new_password: '', new_password_confirmation: '' })

const emailSubmitting = ref(false)
const passwordSubmitting = ref(false)
const emailError = ref('')
const passwordError = ref('')
const emailSuccess = ref('')
const passwordSuccess = ref('')

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
