import Vue from 'vue'
import Router from 'vue-router'
import InOutSeparation from "@/views/InOutSeparation";

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'inoutsep',
      component: InOutSeparation
    },
    {
      path: '/time',
      name: 'time',
      component: () => import(/* webpackChunkName: "about" */ './views/TimeSeparation.vue')
    }
  ]
})
