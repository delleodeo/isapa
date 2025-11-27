<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Manage Users</h1>

    <!-- Search Bar -->
    <div class="mb-6">
      <div class="relative max-w-xl">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by name, email, or role..."
          class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-spnr-blue-300 focus:border-transparent"
        />
        <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>
    </div>

    <div class="mb-6 flex gap-4">
      <button @click="filterRole = ''" :class="filterRole === '' ? 'btn-primary' : 'btn-outline'" class="text-sm">All</button>
      <button @click="filterRole = 'customer'" :class="filterRole === 'customer' ? 'btn-primary' : 'btn-outline'" class="text-sm">Customers</button>
      <button @click="filterRole = 'owner'" :class="filterRole === 'owner' ? 'btn-primary' : 'btn-outline'" class="text-sm">Shop Owners</button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
    </div>

        <div v-else>
          <!-- Desktop table -->
          <div class="hidden sm:block card overflow-x-auto overflow-hidden border border-blue-300">
            <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in filteredUsers" :key="user.id">
            <td class="px-6 py-4 text-sm text-gray-900">{{ user.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-600">{{ user.email }}</td>
            <td class="px-6 py-4 text-sm"><span class="px-2 py-1 bg-spnr-blue-100 text-spnr-blue-900 rounded-full text-xs">{{ user.role }}</span></td>
            <td class="px-6 py-4 text-sm">
              <span :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded-full text-xs">
                {{ user.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm">
              <button @click="toggleStatus(user)" class="text-spnr-blue-900 hover:text-spnr-blue-700">
                {{ user.is_active ? 'Deactivate' : 'Activate' }}
              </button>
            </td>
          </tr>
        </tbody>
        </table>
      </div>

      <!-- Mobile cards -->
      <div class="sm:hidden space-y-4">
        <div v-for="user in filteredUsers" :key="user.id" class="card bg-gray-50 overflow-hidden border border-blue-300 p-3">
          <div v-if="compactMobile" class="flex items-center justify-between">
            <div>
              <div class="text-sm font-semibold text-gray-800">{{ user.name }}</div>
              <div class="text-xs text-gray-500">{{ user.email }}</div>
            </div>
            <div class="text-right">
              <div class="text-xs font-medium" :class="user.is_active ? 'text-green-700' : 'text-red-700'">{{ user.is_active ? 'Active' : 'Inactive' }}</div>
              <div class="text-xs mt-1 text-gray-600">{{ user.role }}</div>
            </div>
          </div>
          <div v-else class="flex flex-col gap-2">
            <div class="flex justify-between items-center">
              <div>
                <div class="text-sm font-semibold text-gray-800">{{ user.name }}</div>
                <div class="text-xs text-gray-500">{{ user.email }}</div>
              </div>
              <div class="text-right">
                <div :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded-full text-xs">{{ user.is_active ? 'Active' : 'Inactive' }}</div>
                <div class="text-xs text-gray-600 mt-1">{{ user.role }}</div>
              </div>
            </div>
            <div class="pt-2 border-t border-gray-100 flex justify-end">
              <button @click="toggleStatus(user)" class="text-spnr-blue-900 hover:text-spnr-blue-700 text-sm">{{ user.is_active ? 'Deactivate' : 'Activate' }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import api from '../../services/api'

const loading = ref(true)
const users = ref([])
const filterRole = ref('')
const searchQuery = ref('')
const compactMobile = ref(false)
const updateCompact = () => { compactMobile.value = window.innerWidth < 640 }

const filteredUsers = computed(() => {
  let result = users.value

  // Filter by role
  if (filterRole.value) {
    result = result.filter(u => u.role === filterRole.value)
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(u => {
      return (
        u.name?.toLowerCase().includes(query) ||
        u.email?.toLowerCase().includes(query) ||
        u.role?.toLowerCase().includes(query)
      )
    })
  }

  return result
})

const fetchUsers = async () => {
  const response = await api.get('/admin/users')
  users.value = response.data
  loading.value = false
}

const toggleStatus = async (user) => {
  await api.put(`/admin/users/${user.id}/toggle`)
  fetchUsers()
}

onMounted(() => {
  fetchUsers()
  updateCompact()
  window.addEventListener('resize', updateCompact)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateCompact)
})
</script>
