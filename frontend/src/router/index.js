import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/views/LoginView.vue'),
      meta: { guest: true }
    },
    {
      path: '/order/:outletId/:tableId',
      name: 'QROrder',
      component: () => import('@/views/QROrderView.vue'),
      meta: { public: true }
    },
    {
      path: '/',
      component: () => import('@/layouts/MainLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'Dashboard',
          component: () => import('@/views/DashboardView.vue')
        },
        {
          path: 'pos',
          name: 'POS',
          component: () => import('@/views/POSView.vue'),
          meta: { roles: ['kasir', 'owner', 'supervisor'] }
        },
        {
          path: 'products',
          name: 'Products',
          component: () => import('@/views/ProductsView.vue')
        },
        {
          path: 'outlets',
          name: 'Outlets',
          component: () => import('@/views/OutletsView.vue'),
          meta: { roles: ['owner'] }
        },
        {
          path: 'transactions',
          name: 'Transactions',
          component: () => import('@/views/TransactionsView.vue')
        },
        {
          path: 'reports',
          name: 'Reports',
          component: () => import('@/views/ReportsView.vue'),
          meta: { roles: ['owner', 'supervisor'] }
        }
      ]
    }
  ]
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Initialize auth on first navigation only
  if (!authStore.initialized && !authStore.loading) {
    await authStore.initAuth()
  }
  
  const isAuthenticated = !!authStore.user
  const isPublicRoute = to.meta.public === true
  const isGuestRoute = to.meta.guest === true
  const requiresAuth = to.meta.requiresAuth === true
  
  // Public routes - always allow
  if (isPublicRoute) {
    return next()
  }
  
  // Guest routes (login) - redirect to home if already authenticated
  if (isGuestRoute && isAuthenticated) {
    return next('/')
  }
  
  // Protected routes - redirect to login if not authenticated
  if (requiresAuth && !isAuthenticated) {
    return next('/login')
  }
  
  // Role check for protected routes
  if (requiresAuth && to.meta.roles) {
    if (!to.meta.roles.includes(authStore.user?.role)) {
      return next('/')
    }
  }
  
  // Allow navigation
  next()
})

export default router
