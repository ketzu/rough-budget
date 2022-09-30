import Vue from 'vue'
import router from './router'
import Budget from './Budget.vue'
import store from './store'
import vuetify from './plugins/vuetify'
import '@babel/polyfill'
import VueTour from "vue-tour";

Vue.config.productionTip = false;

Vue.mixin({
  methods: {
    formatcurrency(value) {
      let currency = "USD";
      switch (this.$store.getters.currency) {
        case "€": currency = "EUR"; break;
        case "£": currency = "GBP"; break;
      }
      return Intl.NumberFormat(undefined, { style: "currency", currency: currency, maximumFractionDigits: 0, minimumFractionDigits: 0 }).format(value);
    },
    firstuppercase(string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    },
    typename(type) {
      switch (type) {
        case "daily":
          return "day";
        case "weekly":
          return "week";
        case "monthly":
          return "month";
        case "yearly":
          return "year";
      }
    }
  }
});

store.subscribe((mutation, state) => {
  localStorage.setItem('budget-v3', JSON.stringify(state));
});

require('vue-tour/dist/vue-tour.css');

Vue.use(VueTour);

new Vue({
  router,
  store,
  vuetify,
  render: h => h(Budget),
  beforeCreate() {
    this.$store.commit('initstore');
  }
}).$mount('#app')
