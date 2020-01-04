import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/inout'
    },
    {
      path: '/summary',
      name: 'summary',
      component: () => import('./views/Summary.vue')
    },
    {
      path: '/inout',
      name: 'inoutsep',
      component: () => import('./views/InOutSeparation.vue')
    },
    {
      path: '/time',
      name: 'time',
      component: () => import('./views/TimeSeparation.vue')
    },
    {
      path: '/trackings',
      name: 'trackings',
      component: () => import('./views/Trackings.vue')
    }
  ]
})
