<template>
  <v-app>
    <v-app-bar dark app short color="accent">
      <v-toolbar-title class="d-none d-sm-block">
        <v-img src="banner.png" max-height="32" alt="Rough-Budget" max-width="63"></v-img>
      </v-toolbar-title>

        <v-tabs
            background-color="transparent"
            optional
            grow
            icons-and-text
        >
          <v-tab router to="/">
            <v-icon>
              fas fa-chart-pie
            </v-icon>
            <span class="d-none d-md-block">
              Statistics
            </span>
          </v-tab>
          <v-tab router to="/inout">
            <v-icon>
              fas fa-th-list
            </v-icon>
            <span class="d-none d-md-block">
              Bookkeeping
            </span>
          </v-tab>
          <v-tab router to="/time">
            <v-icon>
              fas fa-calendar-week
            </v-icon>
            <span class="d-none d-md-block">
              Time
            </span>
          </v-tab>
          <v-tab router to="/trackings">
            <v-icon>
              fas fa-chart-line
            </v-icon>
            <span class="d-none d-md-block">
              Log
            </span>
          </v-tab>
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