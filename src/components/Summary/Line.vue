<template>
  <v-card>
    <v-card-text>
      <h2>
        TL;DR: You {{makelose}}
        {{formatcurrency(Math.abs(balance))}} monthly.
      </h2>
    </v-card-text>
  </v-card>
</template>

<script>
  import {mapGetters} from 'vuex'

  export default {
    name: "Line.vue",
    computed: {
      ...mapGetters(['balance']),
      makelose() {
        return this.balance >= 0 ? 'are making' : 'are losing';
      },
    }
  }
</script>

<style scoped>

</style>