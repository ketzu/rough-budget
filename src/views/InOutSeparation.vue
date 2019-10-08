<template>
  <v-container grid-list-lg>
    <v-row md12 v-if="showsummary">
      <summary-chart></summary-chart>
    </v-row>

    <v-row md6>
      <category type="income"></category>
    </v-row>
    <v-row md6>
      <category type="expense"></category>
    </v-row>

    <v-row :key="index" md3 v-for="(entry, index) in trackings">
      <tracking :index="index"></tracking>
    </v-row>
  </v-container>
</template>

<script>
  import {mapGetters} from 'vuex';
  import Category from "@/components/Category";
  import Tracking from "@/components/Tracking";
  import Summary from "@/components/Summary";

  export default {
    name: "InOutSeparation.vue",
    components: {
      'tracking': Tracking,
      'summary-chart': Summary,
      'category': Category
    },
    computed: {
      ...mapGetters(['trackings']),
      showsummary() {
        return this.$store.getters.anyEntries;
      }
    }
  }
</script>

<style scoped>

</style>