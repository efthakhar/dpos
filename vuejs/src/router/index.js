import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import PosView from '../views/PosView.vue'


const router = createRouter({
  history: createWebHistory(document.location.origin+document.location.pathname+'?page=dpos#'),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },

    {
      path: '/pos',
      name: 'pos',
      component: PosView
    }
  ]
})

export default router
