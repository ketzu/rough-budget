<template>
  <v-app>
    <v-toolbar dark fixed app>
      <v-toolbar-title>
        <a href="#"><b><img src="banner.png" height="30" alt="Rough-Budget" width="204"></b></a>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <button id="sidemenutoggle" type="button" @click="sidemenu = !sidemenu">
        <v-icon>fas fa-bars</v-icon>
      </button>
    </v-toolbar>

    <v-navigation-drawer fixed v-model="sidemenu" app right>
      <settings></settings>
      <!--<account v-if="showaccount"></account>-->
      <!--<feedback v-if="showfeedback" v-on:feedback-submit="showfeedback = false"></feedback>-->
    </v-navigation-drawer>

    <v-content class="blue darken-2">
      <v-container grid-list-lg>
        <v-layout align-start row fill-height wrap>
          <v-flex md12 v-if="showsummary">
            <summary-chart></summary-chart>
          </v-flex>
          <v-flex :key="key" md3 v-for="(entry,key) in entries">
            <category :type="key"></category>
          </v-flex>
          <v-flex :key="index" md3 v-for="(entry, index) in trackings">
            <tracking :index="index"></tracking>
          </v-flex>
        </v-layout>
      </v-container>
    </v-content>
    <v-footer app dark height="auto">
      <v-layout align-center justify-center row fill-height>
        <a href="impressum.html"><v-btn flat>{{translate("Privacy Policy")}}</v-btn></a>
        <a href="https://paypal.me/roughbudget"><v-btn flat>{{translate("Donate")}}</v-btn></a>
        <a href="mailto:contact@rough-budget.com"> <v-btn flat>{{translate("Contact")}}</v-btn></a>
      </v-layout>
    </v-footer>
  </v-app>
</template>

<script>
  import {mapGetters} from 'vuex'
  import Settings from './components/Settings.vue'
  import SettingsMix from './components/settingsmixin'
  import Category from './components/Category.vue'
  import Summary from './components/Summary.vue'
  import Tracking from './components/Tracking.vue'
  import Feedback from './components/Feedback.vue'
  import Account from './components/Account.vue'

  export default {
    name: 'budget',
    components: {
      'settings': Settings,
      'category': Category,
      'tracking': Tracking,
      'summary-chart': Summary,
      'feedback': Feedback,
      'account': Account
    },
    data() {
      return {
        sidemenu: false,
        steps: [
          {
            target: '#dailycard',
            content: "These list all your incomes and expenses per day, week, month and year.",
            params: {
              placement: 'top'
            }
          },
          {
            target: '#dailyinput',
            content: "Enter a name for your income or expense here."
          },
          {
            target: '#summarycard',
            content: "After you entered your data, you will find a short analysis and summary here."
          }
        ],
        translation: {
          "de": {
            "Donate": "Spenden",
            "Contact": "Kontakt",
            "Privacy Policy": "Datenschutz & Impressum",
            "These list all your incomes and expenses per day, week, month and year.": "Hier werden ihre Einkünfte und Ausgaben pro Tag, Woche, Monat und Jahr aufgelistet.",
            "After you entered your data, you will find a short analysis and summary here.": "Nachdem Sie ihre Daten eingegeben haben, finden SIe hier eine kurze Zusammenfassung."
          }
        }
      }
    },
    computed: {
      ...mapGetters(['entries', 'currency', 'balance', 'trackings', 'lang']),
      showsummary() {
        return this.$store.getters.anyEntries;
      }
    },
    mixins: [SettingsMix]
  }
</script>

<style scoped>
</style>