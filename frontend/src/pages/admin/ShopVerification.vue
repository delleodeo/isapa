<template>
  <div>
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-3">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Shop Verification</h1>
      <div class="flex flex-wrap sm:flex-nowrap gap-2">
        <button
          @click="filterStatus = 'all'"
          :class="filterStatus === 'all' ? 'btn-primary' : 'btn-outline'"
          class="text-sm px-4"
        >
          All
        </button>
        <button
          @click="filterStatus = 'pending'"
          :class="filterStatus === 'pending' ? 'btn-primary' : 'btn-outline'"
          class="text-sm px-4"
        >
          Pending ({{ pendingCount }})
        </button>
        <button
          @click="filterStatus = 'approved'"
          :class="filterStatus === 'approved' ? 'btn-primary' : 'btn-outline'"
          class="text-sm px-4"
        >
          Approved
        </button>
        <button
          @click="filterStatus = 'rejected'"
          :class="filterStatus === 'rejected' ? 'btn-primary' : 'btn-outline'"
          class="text-sm px-4"
        >
          Rejected
        </button>
      </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
      <div class="relative max-w-xl">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by shop name, owner name, or address..."
          class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-spnr-blue-300 focus:border-transparent"
        />
        <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
      <p class="mt-4 text-gray-600">Loading shops...</p>
    </div>

    <!-- Shops List -->
    <div v-else-if="filteredShops.length > 0" class="space-y-4">
      <div
        v-for="shop in filteredShops"
        :key="shop.id"
        class="card hover:shadow-lg transition-shadow overflow-hidden border border-blue-300"
      >
        <div class="flex flex-col sm:flex-row justify-between items-start mb-4 gap-3">
          <div>
            <h3 class="text-xl font-semibold text-gray-800">{{ shop.shop_name }}</h3>
            <p class="text-sm text-gray-600">Owner: {{ shop.owner.name }}</p>
            <p class="text-sm text-gray-600">Email: {{ shop.owner.email }}</p>
          </div>
          <span
            :class="{
              'bg-yellow-100 text-yellow-800': shop.verification_status === 'pending',
              'bg-green-100 text-green-800': shop.verification_status === 'approved',
              'bg-red-100 text-red-800': shop.verification_status === 'rejected'
            }"
            class="px-3 py-1 rounded-full text-sm font-medium"
          >
            {{ shop.verification_status.toUpperCase() }}
          </span>
        </div>

        <div class="p-4 sm:p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <p class="text-sm font-medium text-gray-700">Description:</p>
              <p class="text-sm text-gray-600">{{ shop.description || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-700">Address:</p>
              <p class="text-sm text-gray-600">{{ shop.address }}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-700">Contact:</p>
              <p class="text-sm text-gray-600">{{ shop.contact_number }}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-700">Registered:</p>
              <p class="text-sm text-gray-600">{{ new Date(shop.created_at).toLocaleDateString() }}</p>
            </div>
          </div>

          <!-- Verification Documents -->
          <div class="border-t pt-4 mb-4">
            <p class="text-sm font-semibold text-gray-800 mb-3">Verification Documents:</p>
            <div class="flex gap-3">
                <a
                  v-if="shop.business_permit_path"
                  :href="`${apiBaseUrl}/storage/${shop.business_permit_path}`"
                  target="_blank"
                  class="inline-flex flex-1 min-w-0 items-center gap-2 p-2 sm:p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
                >
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-sm text-blue-900">Business Permit</span>
              </a>
              <a
                v-if="shop.owner_id_path"
                :href="`${apiBaseUrl}/storage/${shop.owner_id_path}`"
                target="_blank"
                class="inline-flex flex-1 min-w-0 items-center gap-2 p-2 sm:p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
              >
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                </svg>
                <span class="text-sm text-green-900">Owner's ID</span>
              </a>
              <a
                v-if="shop.proof_of_address_path"
                :href="`${apiBaseUrl}/storage/${shop.proof_of_address_path}`"
                target="_blank"
                class="inline-flex flex-1 min-w-0 items-center gap-2 p-2 sm:p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
              >
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-sm text-purple-900">Proof of Address</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Rejection Reason -->
        <div v-if="shop.rejection_reason" class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
          <p class="text-sm font-medium text-red-800">Rejection Reason:</p>
          <p class="text-sm text-red-700">{{ shop.rejection_reason }}</p>
        </div>

        <!-- Action Buttons (for pending shops) -->
        <div v-if="shop.verification_status === 'pending'" class="flex flex-col sm:flex-row sm:justify-end gap-3">
          <button
            @click="approveShop(shop.id)"
            class="w-full sm:w-auto px-5 py-2 bg-green-700 text-white text-sm rounded-lg hover:bg-green-700 transition-colors font-medium"
            :disabled="processing"
          >
            Approve
          </button>
          <button
            @click="openRejectModal(shop)"
            class="w-full sm:w-auto px-4 py-2 bg-red-700 text-white text-sm rounded-lg hover:bg-red-700 transition-colors font-medium"
            :disabled="processing"
          >
            Reject
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
      </svg>
      <p class="text-gray-600">No {{ filterStatus !== 'all' ? filterStatus : '' }} shops found</p>
    </div>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Reject Shop Verification</h3>
        <p class="text-sm text-gray-600 mb-4">Please provide a reason for rejecting {{ selectedShop?.shop_name }}</p>
        
        <textarea
          v-model="rejectionReason"
          rows="4"
          class="input-field"
          placeholder="Enter rejection reason..."
        ></textarea>

        <div class="flex gap-3 mt-4">
          <button
            @click="closeRejectModal"
            class="flex-1 btn-outline"
          >
            Cancel
          </button>
          <button
            @click="rejectShop"
            class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
            :disabled="!rejectionReason || processing"
          >
            Confirm Rejection
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../services/api'

const shops = ref([])
const loading = ref(true)
const processing = ref(false)
const filterStatus = ref('all')
const searchQuery = ref('')
const showRejectModal = ref(false)
const selectedShop = ref(null)
const rejectionReason = ref('')

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

const filteredShops = computed(() => {
  let filtered = shops.value
  
  // Filter by status
  if (filterStatus.value !== 'all') {
    filtered = filtered.filter(shop => shop.verification_status === filterStatus.value)
  }
  
  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(shop => {
      return (
        shop.shop_name?.toLowerCase().includes(query) ||
        shop.owner?.name?.toLowerCase().includes(query) ||
        shop.owner?.email?.toLowerCase().includes(query) ||
        shop.address?.toLowerCase().includes(query) ||
        shop.contact_number?.toLowerCase().includes(query)
      )
    })
  }
  
  return filtered
})

const pendingCount = computed(() => {
  return shops.value.filter(shop => shop.verification_status === 'pending').length
})

const fetchShops = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/shops')
    shops.value = response.data
  } catch (error) {
    console.error('Error fetching shops:', error)
    alert('Failed to load shops')
  } finally {
    loading.value = false
  }
}

const approveShop = async (shopId) => {
  if (!confirm('Are you sure you want to approve this shop?')) return

  processing.value = true
  try {
    await api.put(`/admin/shops/${shopId}/approve`)
    alert('Shop approved successfully!')
    await fetchShops()
  } catch (error) {
    console.error('Error approving shop:', error)
    alert('Failed to approve shop')
  } finally {
    processing.value = false
  }
}

const openRejectModal = (shop) => {
  selectedShop.value = shop
  rejectionReason.value = ''
  showRejectModal.value = true
}

const closeRejectModal = () => {
  showRejectModal.value = false
  selectedShop.value = null
  rejectionReason.value = ''
}

const rejectShop = async () => {
  if (!rejectionReason.value.trim()) {
    alert('Please provide a rejection reason')
    return
  }

  processing.value = true
  try {
    await api.put(`/admin/shops/${selectedShop.value.id}/reject`, {
      reason: rejectionReason.value
    })
    alert('Shop verification rejected')
    closeRejectModal()
    await fetchShops()
  } catch (error) {
    console.error('Error rejecting shop:', error)
    alert('Failed to reject shop')
  } finally {
    processing.value = false
  }
}

onMounted(() => {
  fetchShops()
})
</script>
