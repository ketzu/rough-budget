<template>
  <v-app>
    <v-app-bar app color="accent" theme="dark">
      <v-toolbar-title class="d-none d-sm-block">
        <v-img src="banner.png" max-height="32" alt="Rough-Budget" max-width="63"></v-img>
      </v-toolbar-title>

          <v-tabs
            color="white"
            optional
            grow
        >
          <v-tab class="icon-label" router to="/summary">
            <v-icon class="me-2">
              fas fa-chart-pie
            </v-icon>
            <span class="d-none d-md-block">
              Statistics
            </span>
          </v-tab>
          <v-tab class="icon-label" router to="/inout">
            <v-icon class="me-2">
              fas fa-th-list
            </v-icon>
            <span class="d-none d-md-block">
              Bookkeeping
            </span>
          </v-tab>
          <v-tab class="icon-label" router to="/time">
            <v-icon class="me-2">
              fas fa-calendar-week
            </v-icon>
            <span class="d-none d-md-block">
              Time
            </span>
          </v-tab>
          <v-tab class="icon-label" router to="/trackings">
            <v-icon class="me-2">
              fas fa-chart-line
            </v-icon>
            <span class="d-none d-md-block">
              Log
            </span>
          </v-tab>
        </v-tabs>

      <v-btn v-show="false" icon id="helptoggle" @click="help = !help">
        <v-icon>fas fa-question</v-icon>
      </v-btn>
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

    <v-main class="bg-primary">
      <router-view/>
    </v-main>
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
        dialog: false,
        help: false
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
.icon-label {
  gap: 0.5rem;
}
</style>
