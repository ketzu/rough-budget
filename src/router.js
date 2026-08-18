import { createRouter, createWebHashHistory } from 'vue-router'

export default createRouter({
  history: createWebHashHistory(),
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
