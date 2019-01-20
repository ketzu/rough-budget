import '@babel/polyfill'
import Vue from 'vue'
import './plugins/vuetify'
import Budget from './Budget.vue'
import store from './store'

Vue.config.productionTip = false

Vue.mixin({
  methods: {
    formatcurrency(value) {
      let currency = "USD";
      switch (this.$store.getters.currency) {
        case "€": currency = "EUR"; break;
        case "£": currency = "GBP"; break;
      }
      return Intl.NumberFormat(undefined, { style: "currency", currency: currency, maximumFractionDigits: 0, minimumFractionDigits: 0 }).format(value);
      // return value.toFixed(this.$store.getters.precision).toString().replace(/\B(?=(\d{3})+(?!\d))/g, this.$store.getters.separator);
    },
    firstuppercase(string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    },
    translate(string) {
      if (this.lang === "en") {
        return string;
      }
      return this.translation[this.lang][string];
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

new Vue({
  store,
  render: h => h(Budget),
  beforeCreate() {
    this.$store.commit('initstore');
  }
}).$mount('#app')
