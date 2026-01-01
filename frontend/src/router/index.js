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

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  // Allow public routes
  if (to.meta.public) {
    next()
  } else if (to.meta.requiresAuth && !authStore.token) {
    next('/login')
  } else if (to.meta.guest && authStore.token) {
    next('/')
  } else if (to.meta.roles && !to.meta.roles.includes(authStore.user?.role)) {
    next('/')
  } else {
    next()
  }
})

export default router
