<template>
  <v-app>
    <v-app-bar dark app color="accent">
      <v-toolbar-title>
        <img src="banner.png" height="32" alt="Rough-Budget" width="63" class="mr-1" style="margin-bottom: -5px;">
      </v-toolbar-title>

        <v-tabs
            background-color="transparent"
            optional
            fixed-tabs
        >
          <v-tab router to="/">Summary</v-tab>
          <v-tab router to="/inout">Income/Expense</v-tab>
          <v-tab router to="/time">Time View</v-tab>
          <v-tab router to="/tracking">Trackings</v-tab>
        </v-tabs>

      <v-btn icon id="sidemenutoggle" @click="sidemenu = !sidemenu">
        <v-icon>fas fa-user</v-icon>
      </v-btn>
    </v-app-bar>

    <v-navigation-drawer fixed v-model="sidemenu" app right>
      <v-container>
        <settings></settings>
        <account></account>
      </v-container>
    </v-navigation-drawer>

    <v-content class="primary">
      <router-view/>
    </v-content>
    <bottom-footer></bottom-footer>
  </v-app>
</template>

<script>
  import {mapGetters} from 'vuex'
  import Settings from './components/Settings.vue'
  import SettingsMix from './components/settingsmixin'
  import Account from './components/Account.vue'
  import Footer from "@/components/Footer";

  export default {
    name: 'budget',
    components: {
      'settings': Settings,
      'account': Account,
      'bottom-footer': Footer
    },
    data() {
      return {
        sidemenu: false,
        dialog: false
      }
    },
    computed: {
      ...mapGetters(['currency', 'balance']),
      showsummary() {
        return this.$store.getters.anyEntries;
      }
    },
    mixins: [SettingsMix]
  }
</script>

<style scoped>
</style>