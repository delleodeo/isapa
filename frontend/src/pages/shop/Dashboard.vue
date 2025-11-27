<template>
  <div>
    <div class="flex justify-between items-center mb-8">
      <h1 :class="compactMobile ? 'text-xl font-bold text-gray-800' : 'text-3xl font-bold text-gray-800'">Shop Dashboard</h1>
      <div class="flex gap-3">
        <!-- Period Filter -->
        <select v-model="selectedPeriod" @change="fetchDashboard" class="input-field w-auto">
          <option value="daily">Today</option>
          <option value="weekly">This Week</option>
          <option value="monthly">This Month</option>
          <option value="yearly">This Year</option>
        </select>

        <!-- Download Report Button -->
        <button @click="downloadReport" class="btn-primary flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Download Report
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
    </div>

    <div v-else>
      <!-- Stats Cards: match admin layout -->
      <div class="mb-8">
        <div class="flex flex-wrap items-stretch gap-4 mb-2">
          <div class="stat-card flex flex-col justify-between border border-blue-300 bg-white p-3 min-w-[110px] md:min-w-[140px] lg:min-w-0 flex-1">
            <p class="text-xs text-gray-600 mb-1">Total Customers</p>
            <h3 class="text-xl md:text-3xl font-bold text-spnr-blue-900">{{ stats.total_customers || 0 }}</h3>
            <p class="text-[10px] text-gray-500 mt-1 md:mt-2">&nbsp;</p>
          </div>
          <div class="stat-card flex flex-col justify-between border border-blue-300 bg-white p-3 min-w-[110px] md:min-w-[140px] lg:min-w-0 flex-1">
            <p class="text-xs text-gray-600 mb-1">Pending Orders</p>
            <h3 class="text-xl md:text-3xl font-bold text-yellow-600">{{ stats.pending_orders || 0 }}</h3>
            <p class="text-[10px] text-gray-500 mt-1 md:mt-2">&nbsp;</p>
          </div>
          <div class="stat-card flex flex-col justify-between border border-blue-300 bg-white p-3 min-w-[110px] md:min-w-[140px] lg:min-w-0 flex-1">
            <p class="text-xs text-gray-600 mb-1">{{ periodLabel }} Orders</p>
            <h3 class="text-xl md:text-3xl font-bold text-green-600">{{ stats.period_orders || 0 }}</h3>
            <p class="text-[10px] text-gray-500 mt-1 md:mt-2">Total: {{ stats.completed_orders || 0 }}</p>
          </div>
          <div class="stat-card flex flex-col justify-between border border-blue-300 bg-white p-3 min-w-[110px] md:min-w-[140px] lg:min-w-0 flex-1">
            <p class="text-xs text-gray-600 mb-1">{{ periodLabel }} Revenue</p>
            <h3 class="text-xl md:text-3xl font-bold text-spnr-blue-900">₱{{ (stats.period_revenue || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</h3>
            <p class="text-[10px] text-gray-500 mt-1 md:mt-2">Total: ₱{{ (stats.total_revenue || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</p>
          </div>
        </div>
      </div>

      <!-- Analytics Chart -->
      <div class="card mb-8 overflow-hidden border border-blue-300">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">{{ chartTitle }}</h2>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 rounded-full bg-gradient-to-r from-spnr-blue-900 to-spnr-blue-600"></div>
              <span class="text-sm text-gray-600">Revenue</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 rounded-full bg-green-500"></div>
              <span class="text-sm text-gray-600">Orders</span>
            </div>
          </div>
        </div>
        
        <!-- Vertical Bar Chart -->
        <div class="relative bg-gray-50 rounded-lg p-6 mx-4 lg:mx-8" :class="selectedPeriod === 'monthly' ? 'h-80' : 'h-96'">
          <div class="flex items-end justify-between h-full gap-2">
            <div v-for="(item, index) in chartData" :key="item.date || item.month" class="flex-1 flex flex-col items-center justify-end group">
              <!-- Revenue Bar -->
              <div class="relative w-full flex flex-col items-center justify-end">
                <div class="absolute -top-8 opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap z-10">
                  <div>₱{{ parseFloat(item.revenue || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 }) }}</div>
                  <div class="text-green-400">{{ item.orders }} orders</div>
                </div>
                <div class="w-full bg-gray-200 rounded-t-lg overflow-hidden transition-all duration-500 hover:shadow-lg relative" 
                     :style="{ height: calculateBarHeight(item.revenue, maxChartRevenue) }">
                  <div class="w-full h-full bg-gradient-to-t from-spnr-blue-900 via-spnr-blue-700 to-spnr-blue-500 relative">
                    <div v-if="item.orders > 0 && parseFloat(item.revenue || 0) > 0" class="absolute top-1 left-1/2 transform -translate-x-1/2">
                      <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-s font-bold shadow-lg">
                        {{ item.orders }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- X-axis Label -->
              <div class="mt-2 text-xs text-gray-600 text-center font-medium" :class="selectedPeriod === 'monthly' ? 'transform -rotate-45 origin-top-left mt-6' : ''">
                <template v-if="selectedPeriod === 'yearly'">{{ getMonthName(item.month) }}</template>
                <template v-else-if="selectedPeriod === 'daily'">Today</template>
                <template v-else>{{ formatChartDate(item.date) }}</template>
              </div>
            </div>
          </div>
          
          <!-- Y-axis Grid Lines -->
          <div class="absolute inset-6 pointer-events-none">
            <div v-for="i in 5" :key="i" class="absolute w-full border-t border-gray-300" :style="{ bottom: `${(i * 20)}%` }">
              <span class="absolute -left-12 -top-2 text-xs text-gray-500">₱{{ formatYAxisLabel(maxChartRevenue * (i / 5)) }}</span>
            </div>
          </div>
        </div>
        
        <!-- Summary Stats -->
        <div class="mt-6 pt-6 border-t border-gray-200" :class="selectedPeriod === 'daily' ? 'grid grid-cols-2 gap-4' : 'grid grid-cols-3 gap-4'">
          <div class="text-center">
            <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
            <p class="text-xl font-bold text-spnr-blue-900">₱{{ totalChartRevenue.toLocaleString('en-PH', { minimumFractionDigits: 2 }) }}</p>
          </div>
          <div class="text-center">
            <p class="text-sm text-gray-600 mb-1">Total Orders</p>
            <p class="text-xl font-bold text-green-600">{{ totalChartOrders }}</p>
          </div>
          <div v-if="selectedPeriod !== 'daily'" class="text-center">
            <p class="text-sm text-gray-600 mb-1">Average per {{ selectedPeriod === 'yearly' ? 'Month' : 'Day' }}</p>
            <p class="text-xl font-bold text-gray-800">₱{{ averageChartRevenue.toLocaleString('en-PH', { minimumFractionDigits: 2 }) }}</p>
          </div>
        </div>
      </div>

      <!-- Top Services -->
      <div class="card overflow-hidden border border-blue-300">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Top Performing Services</h2>
        <div>
          <!-- Desktop table -->
          <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full">
              <thead>
                <tr class="border-b border-gray-200">
                  <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Service</th>
                  <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Orders</th>
                  <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Revenue</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="service in stats.top_services" :key="service.service_id" class="border-b border-gray-100">
                  <td class="py-3 px-4 text-sm text-gray-800">{{ service.service?.service_name || 'Unknown' }}</td>
                  <td class="py-3 px-4 text-sm text-right font-semibold text-gray-800">{{ service.order_count }}</td>
                  <td class="py-3 px-4 text-sm text-right font-semibold text-spnr-blue-900">₱{{ parseFloat(service.revenue || 0).toFixed(2) }}</td>
                </tr>
                <tr v-if="!stats.top_services || stats.top_services.length === 0">
                  <td colspan="3" class="py-8 text-center text-gray-500">No data available</td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Mobile compact cards -->
          <div class="sm:hidden space-y-2">
            <div v-for="service in stats.top_services" :key="service.service_id" class="bg-gray-50 p-3 rounded-lg border border-gray-200">
              <div class="flex justify-between items-start mb-2">
                <span class="text-sm font-semibold text-gray-800 flex-1">{{ service.service?.service_name || 'Unknown' }}</span>
              </div>
              <div class="flex justify-between items-center text-xs">
                <span class="text-gray-600">{{ service.order_count }} orders</span>
                <span class="font-bold text-spnr-blue-900">₱{{ parseFloat(service.revenue || 0).toFixed(2) }}</span>
              </div>
            </div>
            <div v-if="!stats.top_services || stats.top_services.length === 0" class="py-8 text-center text-gray-500 text-sm">
              No data available
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
const selectedPeriod = ref('daily')
const currentYear = new Date().getFullYear()
const compactMobile = ref(false)
// Match admin: shrink header when viewport is below 1024px (lg breakpoint)
const updateCompact = () => { compactMobile.value = window.innerWidth < 1024 }

const stats = ref({
  total_revenue: 0,
  period_revenue: 0,
  total_customers: 0,
  completed_orders: 0,
  period_orders: 0,
  pending_orders: 0,
  daily_sales: [],
  monthly_sales: [],
  top_services: []
})

const periodLabel = computed(() => {
  const labels = {
    daily: 'Today',
    weekly: 'This Week',
    monthly: 'This Month',
    yearly: 'This Year'
  }
  return labels[selectedPeriod.value] || 'Total'
})

// Prepare chart data based on selected period
const chartData = computed(() => {
  const result = []
  
  if (selectedPeriod.value === 'daily') {
    // Show hourly breakdown for today (or summary if not available)
    const salesMap = new Map(stats.value.daily_sales.map(s => [s.date, s]))
    const today = new Date().toISOString().split('T')[0]
    const todayData = salesMap.get(today) || { date: today, revenue: 0, orders: 0 }
    result.push(todayData)
  } else if (selectedPeriod.value === 'weekly') {
    // Show current week (Sunday to Saturday)
    const salesMap = new Map(stats.value.daily_sales.map(s => [s.date, s]))
    const today = new Date()
    const currentDay = today.getDay() // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
    
    // Calculate the start of the week (Sunday)
    const startOfWeek = new Date(today)
    startOfWeek.setDate(today.getDate() - currentDay)
    
    // Generate 7 days from Sunday to Saturday
    for (let i = 0; i < 7; i++) {
      const date = new Date(startOfWeek)
      date.setDate(startOfWeek.getDate() + i)
      const dateStr = date.toISOString().split('T')[0]
      result.push(salesMap.get(dateStr) || { date: dateStr, revenue: 0, orders: 0 })
    }
  } else if (selectedPeriod.value === 'monthly') {
    // Show current month (from 1st to last day of month)
    const salesMap = new Map(stats.value.daily_sales.map(s => [s.date, s]))
    const today = new Date()
    const year = today.getFullYear()
    const month = today.getMonth()
    
    // Get last day of current month
    const lastDay = new Date(year, month + 1, 0)
    const daysInMonth = lastDay.getDate()
    
    // Generate all days in current month
    for (let day = 1; day <= daysInMonth; day++) {
      const date = new Date(year, month, day)
      // Format as YYYY-MM-DD in local timezone
      const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`
      result.push(salesMap.get(dateStr) || { date: dateStr, revenue: 0, orders: 0 })
    }
  } else if (selectedPeriod.value === 'yearly') {
    // Show all 12 months
    const salesMap = new Map(stats.value.monthly_sales.map(s => [s.month, s]))
    for (let month = 1; month <= 12; month++) {
      result.push(salesMap.get(month) || { month, revenue: 0, orders: 0 })
    }
  }
  
  return result
})

const maxChartRevenue = computed(() => {
  if (selectedPeriod.value === 'yearly') {
    return Math.max(...chartData.value.map(d => parseFloat(d.revenue || 0)), 1)
  }
  return Math.max(...chartData.value.map(d => parseFloat(d.revenue || 0)), 1)
})

const chartTitle = computed(() => {
  const titles = {
    daily: 'Today\'s Sales Performance',
    weekly: 'Weekly Sales (Sunday - Saturday)',
    monthly: 'Monthly Sales (Current Month)',
    yearly: 'Monthly Sales Overview (2025)'
  }
  return titles[selectedPeriod.value] || 'Sales Overview'
})

const totalChartRevenue = computed(() => {
  return chartData.value.reduce((sum, item) => sum + parseFloat(item.revenue || 0), 0)
})

const totalChartOrders = computed(() => {
  return chartData.value.reduce((sum, item) => sum + parseInt(item.orders || 0), 0)
})

const averageChartRevenue = computed(() => {
  if (selectedPeriod.value === 'daily') {
    // For daily, just show the total (single day)
    return totalChartRevenue.value
  } else if (selectedPeriod.value === 'weekly') {
    // For weekly, average all 7 days
    const count = chartData.value.filter(item => parseFloat(item.revenue || 0) > 0).length
    return count > 0 ? totalChartRevenue.value / count : 0
  } else if (selectedPeriod.value === 'monthly' || selectedPeriod.value === 'yearly') {
    // For monthly and yearly, exclude today from average calculation
    const today = new Date().toISOString().split('T')[0]
    const currentMonth = new Date().getMonth() + 1
    
    let dataToAverage = chartData.value
    if (selectedPeriod.value === 'monthly') {
      // Exclude today's date
      dataToAverage = chartData.value.filter(item => item.date !== today)
    } else if (selectedPeriod.value === 'yearly') {
      // Exclude current month
      dataToAverage = chartData.value.filter(item => item.month !== currentMonth)
    }
    
    const count = dataToAverage.filter(item => parseFloat(item.revenue || 0) > 0).length
    const total = dataToAverage.reduce((sum, item) => sum + parseFloat(item.revenue || 0), 0)
    return count > 0 ? total / count : 0
  }
  return 0
})

const calculatePercentage = (value, max) => {
  if (max === 0) return 0
  const percentage = (parseFloat(value || 0) / max) * 100
  return Math.max(percentage, 0)
}

const calculateBarHeight = (value, max) => {
  if (max === 0) return '4px'
  const chartHeight = selectedPeriod.value === 'monthly' ? 256 : 320 // h-80 = 320px, h-96 = 384px
  const percentage = (parseFloat(value || 0) / max)
  const height = Math.max(percentage * chartHeight * 0.75, parseFloat(value || 0) > 0 ? 8 : 4) // Use 75% of chart height for bars
  return `${height}px`
}

const formatYAxisLabel = (value) => {
  if (value >= 1000) {
    return (value / 1000).toFixed(1) + 'k'
  }
  return value.toFixed(0)
}

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

const formatChartDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  if (selectedPeriod.value === 'weekly') {
    return date.toLocaleDateString('en-US', { weekday: 'short' })
  }
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

const getMonthName = (monthNum) => {
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  return months[monthNum - 1] || ''
}

const fetchDashboard = async () => {
  loading.value = true
  try {
    const response = await api.get('/shop/dashboard', {
      params: { period: selectedPeriod.value }
    })
    stats.value = response.data
  } catch (error) {
    console.error('Error:', error)
  } finally {
    loading.value = false
  }
}

const downloadReport = async () => {
  try {
    const response = await api.get('/shop/download-report', {
      params: { period: selectedPeriod.value },
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `shop-sales-report-${selectedPeriod.value}-${new Date().toISOString().split('T')[0]}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error downloading report:', error)
  }
}

onMounted(() => {
  fetchDashboard()
  updateCompact()
  window.addEventListener('resize', updateCompact)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateCompact)
})
</script>
