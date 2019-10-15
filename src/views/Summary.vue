<template>
  <v-container>
    <v-row>
      <v-col>
        <summary-line></summary-line>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <overview-chart></overview-chart>
      </v-col>
      <v-col>
        <summary-times></summary-times>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import OverviewChart from '@/components/Summary/OverviewChart';
  import Line from '@/components/Summary/Line';
  import Times from '@/components/Summary/Times';

  export default {
    name: "Summary",
    components: {
      'overview-chart': OverviewChart,
      'summary-line': Line,
      'summary-times': Times,
    }
  }
</script>

<style scoped>

</style>