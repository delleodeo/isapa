<template>
  <div>
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8">Shop Settings</h1>

    <!-- Success Messages -->
    <div v-if="shopSuccess" class="max-w-2xl mx-auto mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between text-sm sm:text-base">
      <span>{{ shopSuccess }}</span>
      <button @click="shopSuccess = ''" class="text-green-700 hover:text-green-900">&times;</button>
    </div>
    <div v-if="profileSuccess" class="max-w-2xl mx-auto mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between text-sm sm:text-base">
      <span>{{ profileSuccess }}</span>
      <button @click="profileSuccess = ''" class="text-green-700 hover:text-green-900">&times;</button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
      <p class="mt-4 text-gray-600">Loading settings...</p>
    </div>

    <div v-else>
      <!-- Shop Information and Verification - Side by Side -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Shop Information -->
        <div class="card overflow-hidden border border-blue-300">
          <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h2 class="text-xl font-semibold text-gray-800">Shop Information</h2>
          </div>
        <form @submit.prevent="updateShop" class="space-y-3">
          <div>
            <label class="label">Shop Photo</label>
            <div class="mt-1">
              <!-- Current Photo Preview -->
              <div v-if="shopForm.photo_path || photoPreview" class="mb-3">
                <img 
                  :src="photoPreview || shopForm.photo_path" 
                  alt="Shop photo" 
                  class="w-24 h-24 object-cover rounded-lg border-2 border-gray-300"
                />
              </div>
              
              <!-- File Input -->
              <input 
                type="file" 
                @change="handlePhotoChange" 
                accept="image/*"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-spnr-blue-50 file:text-spnr-blue-900 hover:file:bg-spnr-blue-100 cursor-pointer"
              />
              <p class="text-xs text-gray-500 mt-1">Recommended: Square image, max 2MB (JPEG, PNG, GIF)</p>
            </div>
          </div>
          <div>
            <label class="label">Shop Name <span class="text-red-500">*</span></label>
            <input v-model="shopForm.shop_name" required class="input-field" placeholder="Enter shop name" />
          </div>
          <div>
            <label class="label">Description</label>
            <textarea v-model="shopForm.description" rows="2" class="input-field" placeholder="Describe your laundry shop services and specialties"></textarea>
          </div>
          <div>
            <label class="label">Address <span class="text-red-500">*</span></label>
            <textarea v-model="shopForm.address" required rows="2" class="input-field" placeholder="Enter complete shop address"></textarea>
          </div>
          <div>
            <label class="label">Contact Number <span class="text-red-500">*</span></label>
            <input v-model="shopForm.contact_number" type="tel" required class="input-field" placeholder="e.g., +1 234 567 8900" />
          </div>
          <div v-if="shopError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ shopError }}
          </div>
          <button type="submit" :disabled="shopSubmitting" class="btn-primary disabled:opacity-50 w-full">
            {{ shopSubmitting ? 'Updating...' : 'Update Shop Information' }}
          </button>
        </form>
        </div>

        <!-- Shop Verification Section -->
        <div class="card overflow-hidden border border-blue-300">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-spnr-blue-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
              <h2 class="text-xl font-semibold text-gray-800">Shop Verification</h2>
            </div>
            <span
              :class="{
                'bg-yellow-100 text-yellow-800': !shopForm.verification_status || shopForm.verification_status === 'pending',
                'bg-green-100 text-green-800': shopForm.verification_status === 'approved',
                'bg-red-100 text-red-800': shopForm.verification_status === 'rejected'
              }"
              class="px-3 py-1 rounded-full text-sm font-medium"
            >
              {{ shopForm.verification_status ? shopForm.verification_status.toUpperCase() : 'NOT SUBMITTED' }}
            </span>
          </div>

          <!-- Verification Status Messages -->
          <div v-if="!shopForm.verification_status" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-blue-800 font-medium mb-2">📋 Verification Required</p>
            <p class="text-sm text-blue-700">Submit your verification documents to get your shop verified and display the verified badge to customers.</p>
          </div>

          <div v-else-if="shopForm.verification_status === 'pending'" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-yellow-800 font-medium mb-2">⏳ Verification Pending</p>
            <p class="text-sm text-yellow-700">Your documents are under review by our admin team. You'll be notified once the verification is complete.</p>
          </div>

          <div v-else-if="shopForm.verification_status === 'approved'" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-green-800 font-medium mb-2">✅ Shop Verified!</p>
            <p class="text-sm text-green-700">Your shop is verified. Customers will see a verified badge on your shop profile.</p>
            <p class="text-xs text-green-600 mt-2">Verified on: {{ new Date(shopForm.verified_at).toLocaleDateString() }}</p>
          </div>

          <div v-else-if="shopForm.verification_status === 'rejected'" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-red-800 font-medium mb-2">❌ Verification Rejected</p>
            <p class="text-sm text-red-700 mb-2">{{ shopForm.rejection_reason || 'Your verification was rejected. Please resubmit with correct documents.' }}</p>
            <p class="text-xs text-red-600">Please upload new documents below and resubmit.</p>
          </div>

          <!-- Verification Documents Form - Only show if not approved -->
          <form v-if="shopForm.verification_status !== 'approved'" @submit.prevent="submitVerification" class="space-y-4">
            <p class="text-sm text-gray-600 mb-3">Upload the following documents for verification (JPG, PNG, or PDF, max 5MB each):</p>

            <div>
              <label class="label">Business Permit / DTI Registration <span class="text-red-500">*</span></label>
              <input
                type="file"
                @change="handleDocumentUpload($event, 'business_permit')"
                accept="image/*,.pdf"
                class="input-field"
              />
              <p class="text-xs text-gray-500 mt-1">
                {{ shopForm.business_permit_path ? '✓ Document uploaded' : 'Upload your business permit or DTI registration' }}
              </p>
            </div>

            <div>
              <label class="label">Valid ID of Owner <span class="text-red-500">*</span></label>
              <input
                type="file"
                @change="handleDocumentUpload($event, 'owner_id_document')"
                accept="image/*,.pdf"
                class="input-field"
              />
              <p class="text-xs text-gray-500 mt-1">
                {{ shopForm.owner_id_path ? '✓ Document uploaded' : 'Government-issued ID (Driver\'s License, Passport, etc.)' }}
              </p>
            </div>

            <div>
              <label class="label">Proof of Business Address <span class="text-red-500">*</span></label>
              <input
                type="file"
                @change="handleDocumentUpload($event, 'proof_of_address')"
                accept="image/*,.pdf"
                class="input-field"
              />
              <p class="text-xs text-gray-500 mt-1">
                {{ shopForm.proof_of_address_path ? '✓ Document uploaded' : 'Utility bill, lease contract, or barangay certificate' }}
              </p>
            </div>

            <div v-if="verificationError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
              {{ verificationError }}
            </div>

            <button
              type="submit"
              :disabled="verificationSubmitting || shopForm.verification_status === 'approved'"
              class="btn-primary disabled:opacity-50 w-full"
            >
              {{ verificationSubmitting ? 'Submitting...' : shopForm.verification_status === 'approved' ? 'Already Verified' : 'Submit for Verification' }}
            </button>
          </form>
        </div>
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
const shopForm = ref({ 
  shop_name: '', 
  description: '', 
  address: '', 
  contact_number: '', 
  photo_path: '',
  verification_status: null,
  business_permit_path: null,
  owner_id_path: null,
  proof_of_address_path: null,
  rejection_reason: null,
  verified_at: null
})
const profileForm = ref({ name: '', phone: '', address: '' })
const photoFile = ref(null)
const photoPreview = ref('')
const documentFiles = ref({
  business_permit: null,
  owner_id_document: null,
  proof_of_address: null
})
const shopSubmitting = ref(false)
const verificationSubmitting = ref(false)
const profileSubmitting = ref(false)
const shopError = ref('')
const verificationError = ref('')
const profileError = ref('')
const shopSuccess = ref('')
const profileSuccess = ref('')

const handlePhotoChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    photoFile.value = file
    // Create preview URL
    const reader = new FileReader()
    reader.onload = (e) => {
      photoPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const handleDocumentUpload = (event, fieldName) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      verificationError.value = `${fieldName} file size must be less than 5MB`
      event.target.value = ''
      return
    }
    documentFiles.value[fieldName] = file
    verificationError.value = ''
  }
}

const loadData = async () => {
  try {
    const shopRes = await api.get('/shop/shop')
    if (shopRes.data) {
      shopForm.value = shopRes.data
    }
    profileForm.value = { 
      name: authStore.user?.name || '',
      phone: authStore.user?.phone || '',
      address: authStore.user?.address || ''
    }
  } catch (error) {
    console.error('Error loading data:', error)
  } finally {
    loading.value = false
  }
}

const updateShop = async () => {
  shopSubmitting.value = true
  shopError.value = ''
  shopSuccess.value = ''
  
  try {
    const formData = new FormData()
    formData.append('shop_name', shopForm.value.shop_name)
    formData.append('description', shopForm.value.description || '')
    formData.append('address', shopForm.value.address)
    formData.append('contact_number', shopForm.value.contact_number)
    formData.append('_method', 'PUT')
    
    if (photoFile.value) {
      formData.append('photo', photoFile.value)
    }
    
    const response = await api.post('/shop/shop/update', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    // Update form with new data including photo path
    if (response.data.shop) {
      shopForm.value = response.data.shop
      photoFile.value = null
      photoPreview.value = ''
    }
    
    shopSuccess.value = 'Shop information updated successfully!'
    setTimeout(() => shopSuccess.value = '', 5000)
  } catch (error) {
    shopError.value = error.response?.data?.message || 'Failed to update shop information'
  } finally {
    shopSubmitting.value = false
  }
}

const updateProfile = async () => {
  profileSubmitting.value = true
  profileError.value = ''
  profileSuccess.value = ''
  
  try {
    const response = await api.put('/shop/profile/update', profileForm.value)
    // Update auth store with new user data
    if (response.data.user) {
      authStore.user = response.data.user
      localStorage.setItem('user', JSON.stringify(response.data.user))
    }
    profileSuccess.value = 'Profile updated successfully!'
    setTimeout(() => profileSuccess.value = '', 5000)
  } catch (error) {
    profileError.value = error.response?.data?.message || 'Failed to update profile'
  } finally {
    profileSubmitting.value = false
  }
}

const submitVerification = async () => {
  // Validate documents
  if (!documentFiles.value.business_permit || !documentFiles.value.owner_id_document || !documentFiles.value.proof_of_address) {
    verificationError.value = 'All verification documents are required'
    return
  }

  verificationSubmitting.value = true
  verificationError.value = ''
  shopSuccess.value = ''
  
  try {
    const formData = new FormData()
    formData.append('shop_name', shopForm.value.shop_name)
    formData.append('description', shopForm.value.description || '')
    formData.append('address', shopForm.value.address)
    formData.append('contact_number', shopForm.value.contact_number)
    formData.append('_method', 'PUT')
    
    // Add verification documents
    formData.append('business_permit', documentFiles.value.business_permit)
    formData.append('owner_id_document', documentFiles.value.owner_id_document)
    formData.append('proof_of_address', documentFiles.value.proof_of_address)
    
    const response = await api.post('/shop/shop/update', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    // Update form with new data
    if (response.data.shop) {
      shopForm.value = response.data.shop
      // Clear document files
      documentFiles.value = {
        business_permit: null,
        owner_id_document: null,
        proof_of_address: null
      }
      // Reset file inputs
      document.querySelectorAll('input[type="file"]').forEach(input => {
        if (input.accept.includes('pdf') || input.accept.includes('image')) {
          input.value = ''
        }
      })
    }
    
    shopSuccess.value = 'Verification documents submitted successfully! Your shop is now pending admin review.'
    setTimeout(() => shopSuccess.value = '', 5000)
  } catch (error) {
    verificationError.value = error.response?.data?.message || 'Failed to submit verification documents'
  } finally {
    verificationSubmitting.value = false
  }
}

onMounted(loadData)
</script>
