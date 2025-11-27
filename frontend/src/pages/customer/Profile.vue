<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-8">My Profile</h1>

    <div class="max-w-2xl mx-auto">
      <!-- Desktop / Wide layout (unchanged) -->
      <div v-if="!isCompact" class="card overflow-hidden border border-blue-300">
        <form @submit.prevent="handleUpdate" class="space-y-6">
          <div>
            <label for="name" class="label">Full Name</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="input-field overflow-hidden border-gray-300"
            />
          </div>

          <div>
            <label for="email" class="label">Email Address</label>
            <input
              id="email"
              v-model="authStore.user.email"
              type="email"
              disabled
              class="input-field bg-gray-100 cursor-not-allowed"
            />
            <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
          </div>

          <div>
            <label for="phone" class="label ">Phone Number</label>
            <input
              id="phone"
              v-model="form.phone"
              type="tel"
              class="input-field overflow-hidden border-gray-300"
              placeholder="e.g., 09XX XXX XXXX"
            />
          </div>

          <!-- Address Section -->
          <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Complete Address</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="house_number" class="label">House/Unit Number</label>
                <input
                  id="house_number"
                  v-model="form.house_number"
                  type="text"
                  class="input-field overflow-hidden border-gray-300"
                  placeholder="e.g., House #123"
                />
              </div>

              <div>
                <label for="street" class="label">Street Name <span class="text-red-500">*</span></label>
                <input
                  id="street"
                  v-model="form.street"
                  type="text"
                  required
                  class="input-field overflow-hidden border-gray-300"
                  placeholder="e.g., JP Rizal Street"
                />
              </div>

              <div>
                <label for="barangay" class="label">Barangay <span class="text-red-500">*</span></label>
                <input
                  id="barangay"
                  v-model="form.barangay"
                  type="text"
                  required
                  class="input-field overflow-hidden border-gray-300"
                  placeholder="e.g., Barangay Macatoc"
                />
              </div>

              <div>
                <label for="city" class="label">City/Municipality <span class="text-red-500">*</span></label>
                <input
                  id="city"
                  v-model="form.city"
                  type="text"
                  required
                  class="input-field overflow-hidden border-gray-300"
                  placeholder="e.g., Victoria"
                />
              </div>

              <div>
                <label for="province" class="label">Province <span class="text-red-500">*</span></label>
                <input
                  id="province"
                  v-model="form.province"
                  type="text"
                  required
                  class="input-field overflow-hidden border-gray-300"
                  placeholder="e.g., Oriental Mindoro"
                />
              </div>

              <div>
                <label for="postal_code" class="label">Postal/ZIP Code <span class="text-red-500">*</span></label>
                <input
                  id="postal_code"
                  v-model="form.postal_code"
                  type="text"
                  required
                  class="input-field overflow-hidden border-gray-300"
                  placeholder="e.g., 5205"
                />
              </div>
            </div>
          </div>

          <div v-if="success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ success }}
          </div>

          <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ error }}
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="btn-primary disabled:opacity-50"
          >
            {{ loading ? 'Updating...' : 'Update Profile' }}
          </button>
        </form>
      </div>

      <!-- Compact mobile/tablet layout: stacked fields, smaller paddings -->
      <div v-else class="bg-white rounded-lg border border-blue-200 p-4">
        <form @submit.prevent="handleUpdate" class="space-y-4">
          <div>
            <label for="name_mobile" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input id="name_mobile" v-model="form.name" type="text" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md" />
          </div>

          <div>
            <label for="email_mobile" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input id="email_mobile" v-model="authStore.user.email" type="email" disabled class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md bg-gray-100 cursor-not-allowed" />
            <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
          </div>

          <div>
            <label for="phone_mobile" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input id="phone_mobile" v-model="form.phone" type="tel" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md" placeholder="e.g., 09XX XXX XXXX" />
          </div>

          <div class="pt-2">
            <h3 class="text-sm font-semibold text-gray-800 mb-2">Complete Address</h3>
            <div class="space-y-3">
              <div>
                <label class="block text-xs text-gray-600">House/Unit Number</label>
                <input v-model="form.house_number" type="text" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md" placeholder="e.g., House #123" />
              </div>

              <div>
                <label class="block text-xs text-gray-600">Street Name <span class="text-red-500">*</span></label>
                <input v-model="form.street" type="text" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md" placeholder="e.g., JP Rizal Street" />
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block text-xs text-gray-600">Barangay <span class="text-red-500">*</span></label>
                  <input v-model="form.barangay" type="text" required class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md" placeholder="Barangay" />
                </div>
                <div>
                  <label class="block text-xs text-gray-600">City/Municipality <span class="text-red-500">*</span></label>
                  <input v-model="form.city" type="text" required class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md" placeholder="City" />
                </div>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block text-xs text-gray-600">Province <span class="text-red-500">*</span></label>
                  <input v-model="form.province" type="text" required class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md" placeholder="Province" />
                </div>
                <div>
                  <label class="block text-xs text-gray-600">Postal/ZIP Code <span class="text-red-500">*</span></label>
                  <input v-model="form.postal_code" type="text" required class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md" placeholder="Postal/ZIP" />
                </div>
              </div>
            </div>
          </div>

          <div v-if="success" class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded text-sm">{{ success }}</div>
          <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded text-sm">{{ error }}</div>
          <div class="flex justify-center">
            <button type="submit" :disabled="loading" class="btn-primary w-1/2 px-3 py-2 text-sm bg-spnr-blue-600 text-white rounded-md">{{ loading ? 'Updating...' : 'Update' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const authStore = useAuthStore()

const form = ref({
  name: '',
  phone: '',
  house_number: '',
  street: '',
  barangay: '',
  city: '',
  province: '',
  postal_code: '',
})

const loading = ref(false)
const success = ref('')
const error = ref('')
const isCompact = ref(false)

const updateCompact = () => {
  isCompact.value = window.innerWidth < 768
}

const loadProfile = () => {
  const user = authStore.user || {}
  form.value = {
    name: user.name || '',
    phone: user.phone || '',
    house_number: user.house_number || '',
    street: user.street || '',
    barangay: user.barangay || '',
    city: user.city || '',
    province: user.province || '',
    postal_code: user.postal_code || '',
  }
}

const handleUpdate = async () => {
  loading.value = true
  success.value = ''
  error.value = ''

  try {
    const response = await api.put('/customer/profile/update', form.value)
    
    // Update local user data
    Object.assign(authStore.user, form.value)
    localStorage.setItem('user', JSON.stringify(authStore.user))
    
    success.value = 'Profile updated successfully!'
    
    setTimeout(() => {
      success.value = ''
    }, 3000)
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update profile. Please try again.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadProfile()
  updateCompact()
  window.addEventListener('resize', updateCompact)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateCompact)
})
</script>
