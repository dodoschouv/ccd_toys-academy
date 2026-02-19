import { createRouter, createWebHistory } from 'vue-router'
import SettingsView from '../views/SettingsView.vue'
import CatalogueView from '../views/CatalogueView.vue'

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
    }

  ],
})



export default router
