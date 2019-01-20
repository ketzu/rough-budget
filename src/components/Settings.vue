<template>
  <v-list dense>
    <v-layout row align-center wrap>
      <v-flex xs10>
        <v-subheader>
          <label for="lang">{{translate("Language")}}:</label>
        </v-subheader>
      </v-flex>
      <v-flex xs10 offset-xs1>
        <v-select id="lang" v-model="lang" :items="['en','de']">
          <template slot="selection" slot-scope="data">
            {{ langname(data.item) }}
          </template>
          <template slot="item" slot-scope="data">
            {{ langname(data.item) }}
          </template>
        </v-select>
      </v-flex>
      <v-flex xs10>
        <v-subheader>
          <label for="currency">{{translate("Currency")}}:</label>
        </v-subheader>
      </v-flex>
      <v-flex xs10 offset-xs1>
        <v-select id="currency" v-model="currency" :items="['$','€','£','₩','¥']"/>
      </v-flex>
    </v-layout>
  </v-list>
  <!--<div class="col-md-2">
    <label for="separator">{{translate("Separator")}}:</label>
    <div class="input-group md-2">
      <select v-model="separator" id="separator" class="custom-select">
        <option value=",">,</option>
        <option value=".">.</option>
        <option value="-">-</option>
      </select>
    </div>
  </div>
  <div class="col-md-2">
    <label for="precision">{{translate("Precision")}}:</label>
    <div class="input-group md-2">
      <select v-model="precision" id="precision" class="custom-select">
        <option value="0">0{{currency}}</option>
        <option value="2">0.00{{currency}}</option>
      </select>
    </div>
  </div>
  <div class="col-md-2">
    <label for="window">{{translate("Window size for average")}}:</label>
    <div class="input-group md-2">
      <input id="window" ref="input" type="number" class="form-control" min="1" v-model="window">
      <div class="input-group-append">
        <span class="input-group-text">{{translate("Days")}}</span>
      </div>
    </div>
  </div>-->
</template>

<script>
  export default {
    name: 'settings',
    data() {
      return {
        translation: {
          "de": {
            "Language": "Sprache",
            "Precision": "Nachkommastellen",
            "Window size for average": "Fenstergröße des Mittelwerts",
            "Separator": "Trennzeichen",
            "Currency": "Währung",
            "Days": "Tage"
          }
        }
      }
    },
    methods: {
      langname(lang) {
        return {'en': 'English', 'de': 'Deutsch'}[lang];
      }
    },
    computed: {
      currency: {
        get() {
          return this.$store.getters.currency
        },
        set(value) {
          this.$store.dispatch('setcurrency', value)
        }
      },
      precision: {
        get() {
          return this.$store.getters.precision
        },
        set(value) {
          this.$store.dispatch('setprecision', value)
        }
      },
      window: {
        get() {
          return this.$store.getters.window
        },
        set(value) {
          this.$store.dispatch('setwindow', value)
        }
      },
      lang: {
        get() {
          return this.$store.getters.lang
        },
        set(value) {
          if (["de", "en"].includes(value)) {
            this.$store.dispatch('setlang', value)
          }
        }
      },
      separator: {
        get() {
          return this.$store.getters.separator
        },
        set(value) {
          this.$store.dispatch('setseparator', value)
        }
      }
    }
  }
</script>

<style scoped>
</style>