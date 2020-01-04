<template>
  <v-container>
    <v-row>
      <v-col :md="6" cols="12">
        <category type="income"></category>
      </v-col>
      <v-col :md="6" cols="12">
        <category type="expense"></category>
      </v-col>
    </v-row>
    <in-out-tour></in-out-tour>
  </v-container>
</template>

<script>
  import Category from "@/components/Category";
  import InOutTour from "../tours/InOutTour";

  export default {
    name: "InOutSeparation.vue",
    components: {
      InOutTour,
      'category': Category
    }
  }
</script>

<style scoped>

</style>
