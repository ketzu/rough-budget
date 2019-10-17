<template>
  <v-card>
    <v-card-text>
      <v-container>
        <v-row justify="center">
          <h3>Budget Value Overview</h3>
        </v-row>
      </v-container>
      <v-list>
        <v-list-item v-for="item in ['daily', 'weekly', 'monthly', 'yearly']" :key="item.title">
          <v-list-item-avatar>
            <v-icon :color='balance < 0 ? "red darken-2" : "blue darken-2"'>fas {{icontype(item)}}</v-icon>
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