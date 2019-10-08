import Vue from 'vue'
import Router from 'vue-router'
import Summary from "@/views/Summary";

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'summary',
      component: Summary
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
