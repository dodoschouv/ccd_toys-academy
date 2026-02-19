import { createRouter, createWebHistory } from 'vue-router'
import SettingsView from '../views/SettingsView.vue'
import CatalogueView from '../views/CatalogueView.vue'
import LoginView from '../views/LoginView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/home',
    },
    {
      path: '/settings',
      name: 'settings', 
      component: SettingsView,
    },
    {
      path: '/catalogue',
      name: 'catalogue',
      component: CatalogueView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    }

  ],
})



export default router
