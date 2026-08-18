import { createApp } from 'vue'
import router from './router'
import Budget from './Budget.vue'
import store from './store'
import vuetify from './plugins/vuetify'
import Vue3Tour from 'vue3-tour'
import 'vue3-tour/dist/vue3-tour.css'

navigator.serviceWorker.getRegistrations().then(function (registrations) {
  for (let registration of registrations) {
    registration.unregister()
  }
})

const app = createApp(Budget)

app.mixin({
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
})

store.subscribe((mutation, state) => {
  localStorage.setItem('budget-v3', JSON.stringify(state));
});

app.use(router)
app.use(store)
app.use(vuetify)
app.use(Vue3Tour)

store.commit('initstore')
app.mount('#app')
