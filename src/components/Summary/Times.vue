<template>
  <v-card>
    <v-card-text>
      Your free budget is:
      <v-list>
        <v-list-item v-for="item in ['daily', 'weekly', 'monthly', 'yearly']" :key="item.title">
          <v-list-item-avatar>
            <v-icon v-if="balance < 0" color="red darken-2">fas {{icontype(item)}}</v-icon>
            <v-icon v-else color="blue darken-2">fas {{icontype(item)}}</v-icon>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>{{formatcurrency(Math.abs(balance/multiplier[item]))}} {{item}}.</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-card-text>
  </v-card>
</template>

<script>
  import {mapGetters} from 'vuex'

  export default {
    name: "Times",
    computed: {
      ...mapGetters(['incomes', 'expenses', 'multiplier', 'balance']),
      daily() {
        return this.helper('daily');
      },
      weekly() {
        return this.helper('weekly');
      },
      monthly() {
        return this.helper('monthly');
      },
      yearly() {
        return this.helper('yearly');
      },
    },
    methods: {
      helper(type) {
        return [this.incomes[type] * this.multiplier[type], this.expenses[type] * this.multiplier[type]];
      },
      icontype(type) {
        switch (type) {
          case "daily":
            return "fa-calendar-day";
          case "weekly":
            return "fa-calendar-week";
          case "monthly":
            return "fa-calendar-alt";
          case "yearly":
            return "fa-calendar-check ";
        }
        return "";
      }
    }
  }
</script>

<style scoped>

</style>