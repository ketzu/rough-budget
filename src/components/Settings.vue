<template>
  <v-container>
    <v-layout row align-center wrap>
      <v-flex xs12>
        <v-select id="lang" v-model="lang" :items="['en','de']" :label="translate('Language')">
          <template slot="selection" slot-scope="data">
            {{ langname(data.item) }}
          </template>
          <template slot="item" slot-scope="data">
            {{ langname(data.item) }}
          </template>
        </v-select>
      </v-flex>
      <v-flex xs12>
        <v-select id="currency" v-model="currency" :items="['$','€','£','₩','¥']" :label="translate('Currency')"/>
      </v-flex>
    </v-layout>
  </v-container>
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