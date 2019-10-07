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
      <v-container grid-list-lg>
        <v-layout align-start row wrap>
          <v-flex md12 v-if="showsummary">
            <summary-chart></summary-chart>
          </v-flex>
          <v-flex :key="key" md3 v-for="(entry,key) in entries" v-if="!dual">
            <category :type="key"></category>
          </v-flex>
          <v-flex md6 v-if="dual">
            <category type="income"></category>
          </v-flex>
          <v-flex md6 v-if="dual">
            <category type="expense"></category>
          </v-flex>
          <v-flex :key="index" md3 v-for="(entry, index) in trackings">
            <tracking :index="index"></tracking>
          </v-flex>
        </v-layout>
      </v-container>
    </v-content>
    <v-footer app dark height="auto">
      <v-layout justify-center row wrap>
        <v-dialog v-model="dialog" scrollable max-width="600px">
          <template v-slot:activator="{ on }">
              <v-btn v-on="on" text>{{translate("Privacy Policy")}}</v-btn>
          </template>
          <v-card>
            <v-card-title>{{translate("Privacy Policy")}}</v-card-title>
            <v-divider></v-divider>
            <v-card-text>
              <v-container grid-list-lg>
                <v-layout align-start row wrap>
                  <v-flex md12>
                    <p>
                      {{privacytext1}}
                    </p>
                    <p>
                      {{privacytext2}}
                    </p>
                    <p>
                      {{privacytext3}}
                    </p>
                  </v-flex>
                  <v-divider></v-divider>
                  <v-flex md12>
                    <h2>David Mödinger</h2>
                  </v-flex>
                  <v-flex md2>
                    <v-icon>fas fa-home</v-icon>
                  </v-flex>
                  <v-flex md10>
                    <h4>Römerstr. 118, 89077 Ulm, Germany</h4>
                  </v-flex>
                  <v-flex md2>
                    <v-icon>fas fa-envelope</v-icon>
                  </v-flex>
                  <v-flex md10>
                    <h4>developer@rough-budget.com</h4>
                  </v-flex>
                  <v-flex md2>
                    <v-icon>fas fa-phone</v-icon>
                  </v-flex>
                  <v-flex md10>
                    <h4>+49 - 177 299 1047</h4>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-2" text @click="dialog = false">Close</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <a href="https://paypal.me/roughbudget" target="_blank"><v-btn text>{{translate("Support us")}}</v-btn></a>
        <a href="mailto:contact@rough-budget.com"> <v-btn text>{{translate("Contact")}}</v-btn></a>
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
  import Account from './components/Account.vue'

  export default {
    name: 'budget',
    components: {
      'settings': Settings,
      'category': Category,
      'tracking': Tracking,
      'summary-chart': Summary,
      'account': Account
    },
    data() {
      return {
        sidemenu: false,
        dialog: false,
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
            "Support us": "Unterstütze uns",
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
      },
      privacytext1() {
        if (this.lang === "de") {
          return "Durch die Nutzung unserer Website erklären Sie sich mit der Erhebung, Verarbeitung und Nutzung von Daten gemäß der nachfolgenden Beschreibung einverstanden. Unsere Website kann grundsätzlich ohne Registrierung besucht werden. Dabei werden Daten wie beispielsweise aufgerufene Seiten bzw. Namen der abgerufenen Datei, Datum und Uhrzeit zu statistischen Zwecken auf dem Server gespeichert, ohne dass diese Daten unmittelbar auf Ihre Person bezogen werden. Diese Daten werden ausschließlich zur Fehlerbehhebung erhoben.";
        }else{
          return "By using our service, you agree to accept the usage and collection of data in the following ways: Accessing our website does not require registration. By accessing our website our servers log information, such as the name of the requested file and date and time. This information will only be processed for error reports.";
        }
      },
      privacytext2() {
        if (this.lang === "de") {
          return "Personenbezogene Daten, insbesondere Name, Adresse oder E-Mail-Adresse werden nur von uns gespeichert, sofern Sie einen Account anlegen und diese Daten als Nutzernamen verwenden. Es werden keine Informationen mit dritten geteilt, außer dem Betreiber der Serverinfrastruktur Strato.";
        }else{
          return "Identifying data, such as your name, adress or e-mail address is only collected if you register an account on our website. These are provided voluntarily and only if you use this information as your username. No information will be shared or processed by third parties, besides the server hoster Strato.";
        }
      },
      privacytext3() {
        if (this.lang === "de") {
          return "Andere personenbezogenen Daten, insbeondere alle eingaben innerhalb der Applikation, werden nur dann zum Server übertragen, falls Sie einen Account verwenden. Diese Daten werden mit ihrem Passwort verschlüsselt und können von uns nicht ohne dieses entschlüsselt werden. Ihr passwort wird in keiner von uns lesbaren form übertragen.";
        }else{
          return "Other identifying data, especially all data used withing the app, is only transmitted to the server if you register an account. If you do so, the data will be encrypted with your password and can not be decrypted by us. Your password is not transmitted in any form that can be read by us.";
        }
      },
      dual: {
        get() {
          return this.$store.getters.dual;
        }
      }
    },
    mixins: [SettingsMix]
  }
</script>

<style scoped>
</style>