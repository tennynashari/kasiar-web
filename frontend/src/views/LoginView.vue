<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-500 to-primary-700 p-3 sm:p-4">
    <div class="max-w-md w-full">
      <div class="card">
        <div class="text-center mb-6 sm:mb-8">
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Unified POS</h1>
          <p class="text-sm sm:text-base text-gray-600 mt-2">Login ke sistem kasir</p>
        </div>

        <form @submit.prevent="handleLogin">
          <div class="mb-4">
            <label class="label text-sm sm:text-base">Email</label>
            <input 
              type="email" 
              v-model="email" 
              class="input text-sm sm:text-base" 
              placeholder="email@example.com"
              required
            >
          </div>

          <div class="mb-4 sm:mb-6">
            <label class="label text-sm sm:text-base">Password</label>
            <input 
              type="password" 
              v-model="password" 
              class="input text-sm sm:text-base" 
              placeholder="••••••••"
              required
            >
          </div>

          <div v-if="error" class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-xs sm:text-sm">
            {{ error }}
          </div>

          <button 
            type="submit" 
            class="btn btn-primary w-full text-sm sm:text-base py-2.5 sm:py-3"
            :disabled="loading"
          >
            {{ loading ? 'Loading...' : 'Login' }}
          </button>
        </form>

        <div class="mt-4 sm:mt-6 text-xs sm:text-sm text-gray-600">
          <p class="font-semibold mb-2">Demo Credentials:</p>
          <ul class="space-y-1">
            <li>Owner: owner@kasir.app / password</li>
            <li>Kasir: kasir@kasir.app / password</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  error.value = ''
  loading.value = true

  try {
    await authStore.login(email.value, password.value)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>
