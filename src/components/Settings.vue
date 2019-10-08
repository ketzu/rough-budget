<template>
  <v-layout row align-center wrap>
      <v-row xs12>
        <v-select id="currency" v-model="currency" :items="['$','€','£','₩','¥']" label="Currency"/>
      </v-row>
    <v-row xs12>
      <v-switch color="blue darken-2" :label="dual ? 'Income/Expenses' : 'Daily to Yearly'" v-model="dual"></v-switch>
    </v-row>
  </v-layout>
</template>

<script>
  export default {
    name: 'settings',
    computed: {
      currency: {
        get() {
          return this.$store.getters.currency;
        },
        set(value) {
          this.$store.dispatch('setcurrency', value);
        }
      },
      dual: {
        get() {
          return this.$store.getters.dual;
        },
        set(value) {
          this.$store.dispatch('setdual', value);
        }
      },
      precision: {
        get() {
          return this.$store.getters.precision;
        },
        set(value) {
          this.$store.dispatch('setprecision', value);
        }
      },
      window: {
        get() {
          return this.$store.getters.window;
        },
        set(value) {
          this.$store.dispatch('setwindow', value);
        }
      },
      separator: {
        get() {
          return this.$store.getters.separator
        },
        set(value) {
          this.$store.dispatch('setseparator', value)
        }
      }
    }
  }
</script>

<style scoped>
</style>