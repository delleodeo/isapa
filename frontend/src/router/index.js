import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../pages/Home.vue'),
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../pages/Login.vue'),
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../pages/Register.vue'),
  },
  {
    path: '/auth/callback',
    name: 'AuthCallback',
    component: () => import('../pages/AuthCallback.vue'),
  },
  {
    path: '/category/:categoryId/services',
    name: 'CategoryServices',
    component: () => import('../pages/CategoryServices.vue'),
  },
  {
    path: '/customer',
    name: 'CustomerLayout',
    component: () => import('../layouts/CustomerLayout.vue'),
    meta: { requiresAuth: true, role: 'customer' },
    children: [
      {
        path: 'dashboard',
        name: 'CustomerDashboard',
        component: () => import('../pages/customer/Dashboard.vue'),
      },
      {
        path: 'shops',
        name: 'Shops',
        component: () => import('../pages/customer/Shops.vue'),
      },
      {
        path: 'services',
        name: 'CustomerServices',
        component: () => import('../pages/customer/Services.vue'),
      },
      {
        path: 'shops/:id/services',
        name: 'CustomerShopServices',
        component: () => import('../pages/customer/ShopServices.vue'),
      },
      {
        path: 'shops/:id/details',
        name: 'CustomerShopDetails',
        component: () => import('../pages/customer/ShopDetails.vue'),
      },
      {
        path: 'orders',
        name: 'CustomerOrders',
        component: () => import('../pages/customer/Orders.vue'),
      },
      {
        path: 'payment/:id',
        name: 'CustomerPayment',
        component: () => import('../pages/customer/Payment.vue'),
      },
      {
        path: 'track',
        name: 'TrackOrder',
        component: () => import('../pages/customer/TrackOrder.vue'),
      },
      {
        path: 'profile',
        name: 'CustomerProfile',
        component: () => import('../pages/customer/Profile.vue'),
      },
      {
        path: 'security',
        name: 'CustomerSecurity',
        component: () => import('../pages/customer/Security.vue'),
      },
    ],
  },
  {
    path: '/shop',
    name: 'ShopLayout',
    component: () => import('../layouts/ShopOwnerLayout.vue'),
    meta: { requiresAuth: true, role: 'owner' },
    children: [
      {
        path: 'dashboard',
        name: 'ShopDashboard',
        component: () => import('../pages/shop/Dashboard.vue'),
      },
      {
        path: 'services',
        name: 'ShopOwnerServices',
        component: () => import('../pages/shop/Services.vue'),
      },
      {
        path: 'orders',
        name: 'ShopOrders',
        component: () => import('../pages/shop/Orders.vue'),
      },
      {
        path: 'settings',
        name: 'ShopSettings',
        component: () => import('../pages/shop/Settings.vue'),
      },
      {
        path: 'profile',
        name: 'ShopProfile',
        component: () => import('../pages/shop/Profile.vue'),
      },
      {
        path: 'security',
        name: 'ShopSecurity',
        component: () => import('../pages/shop/Security.vue'),
      },
    ],
  },
  {
    path: '/admin',
    name: 'AdminLayout',
    component: () => import('../layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: () => import('../pages/admin/Dashboard.vue'),
      },
      {
        path: 'users',
        name: 'AdminUsers',
        component: () => import('../pages/admin/Users.vue'),
      },
      {
        path: 'categories',
        name: 'AdminCategories',
        component: () => import('../pages/admin/Categories.vue'),
      },
      {
        path: 'shops',
        name: 'AdminShopVerification',
        component: () => import('../pages/admin/ShopVerification.vue'),
      },
      {
        path: 'orders',
        name: 'AdminOrders',
        component: () => import('../pages/admin/Orders.vue'),
      },
      {
        path: 'settings',
        name: 'AdminSettings',
        component: () => import('../pages/admin/Settings.vue'),
      },
      {
        path: 'change-email',
        name: 'AdminChangeEmail',
        component: () => import('../pages/admin/ChangeEmail.vue'),
      },
      {
        path: 'change-password',
        name: 'AdminChangePassword',
        component: () => import('../pages/admin/ChangePassword.vue'),
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.role && authStore.getRole() !== to.meta.role) {
    next('/')
  } else {
    next()
  }
})

export default router
