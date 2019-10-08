<template>
  <v-container>
    <v-row>
      <v-col>
        <summary-chart></summary-chart>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import Summary from '@/components/Summary';

  export default {
    name: "Summary",
    components: {
      'summary-chart': Summary
    }
  }
</script>

<style scoped>

</style>