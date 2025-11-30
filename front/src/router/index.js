import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/guest/Login.vue'
import Register from '../views/guest/Register.vue'
import ShowAnnonce from '../views/annonces/ShowAnnonce.vue'
import Paiement from '../views/paiement/Paiement.vue'
import PaymentSuccess from '../views/paiement/PaymentSuccess.vue'
const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  {
    path: '/annonce/:id',
    name: 'ShowAnnonce',
    component: ShowAnnonce
  },
  {
    path: '/paiement/:id',
    name: 'Paiement',
    component: Paiement
  },
  { 
    path: '/payment-success', 
    component: PaymentSuccess 
  }

  // autres routes...
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
