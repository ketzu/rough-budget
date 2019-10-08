<template>
  <v-container>
    <v-row>
      <v-col :key="key" v-for="(entry,key) in entries">
        <category :type="key"></category>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import {mapGetters} from 'vuex';
  import Category from "@/components/Category";

  export default {
    name: "TimeSeparation",
    components: {
      'category': Category
    },
    computed: {
      ...mapGetters(['entries'])
    }
  }
</script>

<style scoped>

</style>