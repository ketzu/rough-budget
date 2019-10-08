<template>
  <v-container>
    <v-row>
      <v-col>
        <category type="income"></category>
      </v-col>
      <v-col>
        <category type="expense"></category>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import Category from "@/components/Category";

  export default {
    name: "InOutSeparation.vue",
    components: {
      'category': Category
    }
  }
</script>

<style scoped>

</style>