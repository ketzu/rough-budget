<template>
  <v-container>
    <v-row>
      <v-col>
        <summary-line></summary-line>
      </v-col>
    </v-row>
    <v-row>
      <v-col md="8">
        <overview-chart></overview-chart>
      </v-col>
      <v-col md="4">
        <summary-times></summary-times>
      </v-col>
      <v-col>
        <expenses-chart></expenses-chart>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import OverviewChart from '@/components/Summary/OverviewChart';
  import Line from '@/components/Summary/Line';
  import Times from '@/components/Summary/Times';
  import ExpensesChart from "../components/Summary/ExpensesChart";

  export default {
    name: "Summary",
    components: {
      ExpensesChart,
      'overview-chart': OverviewChart,
      'summary-line': Line,
      'summary-times': Times,
    }
  }
</script>

<style scoped>

</style>
