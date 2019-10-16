<template>

  <v-card>
    <v-card-title>
      <h4>
        Maybe you would like to track one of your entries?
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
      ...mapGetters(['untrackedincome','untrackedexpense'])
    }
  }
</script>

<style scoped>

</style>