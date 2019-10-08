<template>
  <v-app>
    <v-app-bar dark fixed app>
      <v-toolbar-title>
        <a href="#"><b><img src="banner.png" height="30" alt="Rough-Budget" width="204"></b></a>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon id="sidemenutoggle" @click="sidemenu = !sidemenu">
        <v-icon>fas fa-bars</v-icon>
      </v-btn>
    </v-app-bar>

    <v-navigation-drawer fixed v-model="sidemenu" app right>
      <v-container>
        <settings></settings>
        <account></account>
      </v-container>
    </v-navigation-drawer>

    <v-content class="blue darken-2">

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