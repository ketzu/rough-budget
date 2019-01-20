<template>
  <v-list-tile avatar>
    <v-list-tile-avatar>
      <v-btn icon @click="spending = !spending">
        <v-icon large v-if="!spending" color="blue darken-2">fas fa-arrow-circle-up</v-icon>
        <v-icon large v-if="spending" color="red darken-2">fas fa-arrow-circle-down</v-icon>
      </v-btn>
    </v-list-tile-avatar>

    <v-list-tile-content>
      <v-list-tile-title>
        {{name}}: {{value}}{{currency}}
      </v-list-tile-title>
      <v-list-tile-sub-title>
        {{translateevery()}} {{steps>1 ? steps : ""}} {{translate(typename(type)+(steps > 1 ? "s." : ".  ")).replace(" ", String.fromCharCode(160))}}
      </v-list-tile-sub-title>
    </v-list-tile-content>

    <v-list-tile-action>
      <v-layout row>
        <v-flex xs6>
          <v-btn icon ripple @click="$store.dispatch('newtracking', {type: type, identity: identity})">
            <v-icon color="blue darken-2">fas fa-chart-line</v-icon>
          </v-btn>
        </v-flex>
        <v-flex xs6>
          <v-btn icon ripple @click="$store.dispatch('delentry',{type: type, identity: identity})">
            <v-icon color="grey darken-2">fas fa-times</v-icon>
          </v-btn>
        </v-flex>
      </v-layout>
    </v-list-tile-action>
  </v-list-tile>
</template>

<script>
  import Settings from './settingsmixin'

  export default {
    name: 'entry',
    props: ['identity', 'type'],
    data() {
      return {
        translation: {
          "de": {
            "Expense": "Ausgabe",
            "Income": "Einnahme",
            "days.": "Tag.",
            "weeks.": "Woche.",
            "months.": "Monat.",
            "years.": " Jahr.",
            "day.  ": " Tag.",
            "week.  ": " Woche.",
            "month.  ": " Monat.",
            "year.  ": "  Jahr."
          }
        }
      }
    },
    computed: {
      name() {
        return this.$store.getters[this.type][this.identity].name;
      },
      spending: {
        get() {
          return this.$store.getters[this.type][this.identity].spending
        },
        set(value) {
          this.$store.dispatch('updatespending', {identity: this.identity, type: this.type, value: value})
        }
      },
      value: {
        get() {
          return this.$store.getters[this.type][this.identity].value
        },
        set(value) {
          this.$store.dispatch('updatevalue', {identity: this.identity, type: this.type, value: value})
        }
      },
      steps: {
        get() {
          return this.$store.getters[this.type][this.identity].steps
        },
        set(value) {
          this.$store.dispatch('updatesteps', {identity: this.identity, type: this.type, value: value})
        }
      }
    },
    methods: {
      translateevery() {
        if (this.lang === "en") {
          return "Every";
        }
        switch (this.type) {
          case "daily":
            return "Jeden";
          case "weekly":
            return "Jede";
          case "monthly":
            return "Jeden";
          case "yearly":
            return "Jedes";
        }
      }
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>