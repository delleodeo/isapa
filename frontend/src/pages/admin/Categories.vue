<template>
  <div>
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Manage Categories</h1>
      <button @click="showAddModal = true" class="btn-primary">Add Category</button>
    </div>

    <div v-if="categories.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="category in categories" :key="category.id" class="card overflow-hidden border border-blue-300 flex flex-col">
        <h3 class="text-lg font-semibold text-gray-800">{{ category.name }}</h3>
        <p class="text-sm text-gray-600 mt-2 flex-1">{{ category.description }}</p>
        <p class="text-xs text-gray-500 mt-2">{{ category.services_count }} services</p>
        <div class="flex gap-2 mt-4">
          <button @click="editCategory(category)" class="flex-1 btn-primary text-m">Edit</button>
          <button @click="deleteCategory(category.id)" class="flex-1 bg-red-800 hover:bg-red-700 text-white text-sm py-2 px-4 rounded-lg">Delete</button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ showEditModal ? 'Edit' : 'Add' }} Category</h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="label">Name</label>
            <input v-model="form.name" required class="input-field" />
          </div>
          <div>
            <label class="label">Description</label>
            <textarea v-model="form.description" rows="3" class="input-field"></textarea>
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

const categories = ref([])
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingId = ref(null)
const form = ref({ name: '', description: '' })

const fetchCategories = async () => {
  const response = await api.get('/admin/categories')
  categories.value = response.data
}

const editCategory = (category) => {
  form.value = { ...category }
  editingId.value = category.id
  showEditModal.value = true
}

const handleSubmit = async () => {
  if (showEditModal.value) {
    await api.put(`/admin/categories/${editingId.value}`, form.value)
  } else {
    await api.post('/admin/categories', form.value)
  }
  closeModal()
  fetchCategories()
}

const deleteCategory = async (id) => {
  if (confirm('Delete this category?')) {
    try {
      await api.delete(`/admin/categories/${id}`)
      fetchCategories()
    } catch (error) {
      alert(error.response?.data?.message || 'Cannot delete category')
    }
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  form.value = { name: '', description: '' }
}

onMounted(fetchCategories)
</script>
