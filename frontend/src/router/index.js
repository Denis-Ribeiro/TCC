import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import TutorView from '../views/TutorView.vue'
import QuizView from '../views/QuizView.vue'
import GamesView from '../views/GamesView.vue' // <-- A LINHA DE IMPORTAÇÃO QUE ESTAVA FALTANDO

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/tutor',
      name: 'tutor',
      component: TutorView
    },
    {
      path: '/quiz',
      name: 'quiz',
      component: QuizView
    },
    {
      path: '/games',
      name: 'games',
      component: GamesView // Agora 'GamesView' é reconhecido
    }
  ]
})

export default router

