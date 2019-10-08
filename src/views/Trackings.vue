<template>
<v-container>
  <v-row>
    <v-col :key="index" v-for="(entry, index) in trackings">
      <tracking :index="index"></tracking>
    </v-col>
  </v-row>
</v-container>
</template>

<script>
  import Tracking from "@/components/Tracking";
  import {mapGetters} from 'vuex';

  export default {
    name: "Trackings",
    components: {
      'tracking': Tracking
    },
    computed: {
      ...mapGetters(['trackings']),
    }
  }
</script>

<style scoped>

</style>