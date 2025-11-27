<template>
  <div>
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Manage Services</h1>
      <button @click="showAddModal = true" class="btn-primary">Add Service</button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
    </div>

    <div v-else-if="services.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="service in services" :key="service.id" class="card overflow-hidden border border-blue-300 flex flex-col">
        <h3 class="text-lg font-semibold text-gray-800">{{ service.service_name }}</h3>
        <span class="inline-block mt-1 px-2 py-1 bg-spnr-blue-100 text-spnr-blue-900 text-xs rounded-full">
          {{ service.category?.name }}
        </span>
        <p class="text-gray-700 mt-3 mb-4 flex-1">{{ service.description }}</p>
        <p class="text-2xl font-bold text-spnr-blue-900 mb-4">₱{{ service.price }}<span class="text-sm text-gray-600">/kg</span></p>
        <div class="flex gap-2">
          <button @click="editService(service)" class="flex-1 btn-primary text-sm">Edit</button>
          <button @click="deleteService(service.id)" class="flex-1 bg-red-700 hover:bg-red-800 text-white text-sm py-2 px-4 rounded-lg">Delete</button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ showEditModal ? 'Edit' : 'Add' }} Service</h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="label">Service Name</label>
            <input v-model="form.service_name" required class="input-field" />
          </div>
          <div>
            <label class="label">Category</label>
            <select v-model="form.category_id" required class="input-field">
              <option value="">Select category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
          <div>
            <label class="label">Description</label>
            <textarea v-model="form.description" rows="3" class="input-field"></textarea>
          </div>
          <div>
            <label class="label">Price (₱)</label>
            <input v-model="form.price" type="number" step="0.01" required class="input-field" placeholder="Enter price in pesos" />
          </div>
          <div class="flex gap-3">
            <button type="button" @click="closeModal" class="flex-1 btn-outline">Cancel</button>
            <button type="submit" class="flex-1 btn-primary">{{ showEditModal ? 'Update' : 'Create' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const loading = ref(true)
const services = ref([])
const categories = ref([])
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingId = ref(null)
const form = ref({ service_name: '', category_id: '', description: '', price: '' })

const fetchServices = async () => {
  const response = await api.get('/shop/services')
  services.value = response.data
  loading.value = false
}

const fetchCategories = async () => {
  const response = await api.get('/shop/categories')
  categories.value = response.data
}

const editService = (service) => {
  form.value = { ...service, category_id: service.category_id }
  editingId.value = service.id
  showEditModal.value = true
}

const handleSubmit = async () => {
  try {
    if (showEditModal.value) {
      await api.put(`/shop/services/${editingId.value}`, form.value)
    } else {
      await api.post('/shop/services', form.value)
    }
    closeModal()
    fetchServices()
  } catch (error) {
    console.error('Error:', error)
  }
}

const deleteService = async (id) => {
  if (confirm('Delete this service?')) {
    await api.delete(`/shop/services/${id}`)
    fetchServices()
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  form.value = { service_name: '', category_id: '', description: '', price: '' }
  editingId.value = null
}

onMounted(() => {
  fetchServices()
  fetchCategories()
})
</script>
