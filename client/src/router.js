import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from './views/home.vue'
import About from './views/about.vue'
import Package from './views/package.vue'
import NotFound from './views/notfound.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home
  },
  {
    path: '/packages/:vendor/:name',
    name: 'package',
    component: Package
  },
  {
    path: '/about',
    name: 'about',
    component: About
  },
  {
    path: '*',
    name: 'notfound',
    component: NotFound
  }
]

const router = new VueRouter({
  routes
})

export default router
