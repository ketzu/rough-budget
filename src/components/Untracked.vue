<template>
  <v-card>
    <v-card-title>
      <h4 v-if="untrackedExist">
        Maybe you would like to track some of your entries?
      </h4>
      <h4 v-else-if="!entriesExist">
        You should start by creating some entries in the
        <router-link to="/inout">income/expense</router-link> tab
        or the <router-link to="/time">time view</router-link> tab.
      </h4>
      <h4 v-else>
        You are currently tracking all your entries.
      </h4>
    </v-card-title>
    <v-card-text>
      <v-btn rounded color="primary" ripple dark :key="index" v-for="(entry, index) in untrackedincome" class="ma-1"
             @click="$store.dispatch('newtracking', {type: entry.type, identity: entry.name})"
      >
        {{entry.name}}
      </v-btn>
      <v-btn rounded color="red darken-2" ripple dark :key="index" v-for="(entry, index) in untrackedexpense" class="ma-1"
             @click="$store.dispatch('newtracking', {type: entry.type, identity: entry.name})"
      >
        {{entry.name}}
      </v-btn>
    </v-card-text>
  </v-card>
</template>

<script>
  import {mapGetters} from 'vuex';

  export default {
    name: "Untracked",
    computed: {
      ...mapGetters(['untrackedincome','untrackedexpense']),
      untrackedExist() {
        return Object.keys(this.untrackedincome).length > 0 || Object.keys(this.untrackedexpense).length > 0;
      },
      entriesExist() {
        return Object.keys(this.$store.getters.income).length > 0 ||Object.keys(this.$store.getters.expense).length > 0;
      }
    }
  }
</script>

<style scoped>

</style>