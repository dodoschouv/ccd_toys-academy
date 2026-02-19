import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import CatalogueView from '../views/CatalogueView.vue'
import MaBoxView from '../views/MaBoxView.vue'
import ConnexionView from '../views/ConnexionView.vue'
import BackOfficeView from '../views/BackOfficeView.vue'
import CampaignsView from '../views/back-office/CampaignsView.vue'
import SubscribersView from '../views/back-office/SubscribersView.vue'
import ArticlesView from '../views/back-office/ArticlesView.vue'
import AddArticleView from '../views/back-office/AddArticleView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', redirect: '/home' },
    { path: '/home', name: 'home', component: HomeView },
    { path: '/catalogue', name: 'catalogue', component: CatalogueView },
    { path: '/ma-box', name: 'ma-box', component: MaBoxView },
    { path: '/connexion', name: 'connexion', component: ConnexionView },
    { path: '/login', redirect: '/connexion' },
    { path: '/settings', redirect: '/connexion' },
    {
      path: '/back-office',
      component: BackOfficeView,
      children: [
        { path: 'addarticle', name: 'add-article', component: AddArticleView,},
        { path: 'campaigns', name: 'back-office-campaigns', component: CampaignsView },
        { path: 'subscribers', name: 'back-office-subscribers', component: SubscribersView },
        { path: 'articles', name: 'back-office-articles', component: ArticlesView },
      ],
    },
  ],
})



export default router
