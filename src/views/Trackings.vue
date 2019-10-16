<template>
  <v-container>
    <v-row>
      <v-col>
        <untracked></untracked>
      </v-col>
    </v-row>
    <v-row>
      <v-col :key="index" v-for="(entry, index) in trackings" md="6">
        <tracking :index="index"></tracking>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import Tracking from "@/components/Tracking";
  import {mapGetters} from 'vuex';
  import Untracked from "@/components/Untracked";

  export default {
    name: "Trackings",
    components: {
      'tracking': Tracking,
      'untracked': Untracked
    },
    computed: {
      ...mapGetters(['trackings']),
    }
  }
</script>

<style scoped>

</style>