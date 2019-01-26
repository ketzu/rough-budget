<template>
  <v-list-tile avatar>
    <v-list-tile-avatar>
      <v-btn icon @click="spending = !spending">
        <v-icon large v-if="!spending" color="blue darken-2">fas fa-arrow-circle-up</v-icon>
        <v-icon large v-if="spending" color="red darken-2">fas fa-arrow-circle-down</v-icon>
      </v-btn>
    </v-list-tile-avatar>

      <v-list-tile-content>
        <v-dialog v-model="dialog" max-width="600px">
          <span slot="activator">
            <v-list-tile-title>
              {{name}}: {{value}}{{currency}}
            </v-list-tile-title>
            <v-list-tile-sub-title>
              {{translateevery()}} {{steps>1 ? steps : ""}} {{typeshow(type)}}
            </v-list-tile-sub-title>
          </span>
          <v-card>
            <v-card-title>
              <span class="headline">{{translate("Edit")}}: {{name}}</span>
            </v-card-title>
            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12 sm6>
                    <v-text-field :label="translate('Name')" v-model="name" required></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6>
                    <v-text-field :label="translate('Amount')" :prefix="currency" v-model="value" required></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm4>
                    <v-switch color="red darken-2" :label="!spending ? translate('Income') : translate('Expense')" v-model="spending"></v-switch>
                  </v-flex>
                  <v-flex xs12 sm4>
                    <v-select
                        :items="[1, 2, 3, 4, 5, 6, 7, 8]"
                        :label="translate('')"
                        v-model="steps"
                        :prefix="translateevery()"
                    >
                      <template
                          slot="selection"
                          slot-scope="{item}"
                      >
                        <v-spacer></v-spacer>
                        <span v-if="item>1">
                          {{ item }}
                        </span>
                      </template>
                    </v-select>
                  </v-flex>
                  <v-flex xs12 sm4>
                    <v-select
                        :items="['daily', 'weekly', 'monthly', 'yearly']"
                        :label="translate('')"
                        v-model="date"
                    >
                      <template
                          slot="selection"
                          slot-scope="{item}"
                      >
                        {{ typeshow(item) }}
                      </template>
                      <template
                          slot="item"
                          slot-scope="{item}"
                      >
                        {{ typeshow(item) }}
                      </template>
                    </v-select>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-2" flat @click="dialog = false">Close</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
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
            "Edit": "Bearbeiten",
            "Expense": "Ausgabe",
            "Income": "Einnahme",
            "Amount": "Betrag",
            "Name": "Name",
            "days.": "Tag.",
            "weeks.": "Woche.",
            "months.": "Monat.",
            "years.": "Jahr.",
            "day.": "Tag.",
            "week.": "Woche.",
            "month.": "Monat.",
            "year.": "Jahr.",
            "How many times per ": "Wie oft pro "
          }
        },
        dialog: false
      }
    },
    computed: {
      name: {
        get() {
          return this.$store.getters[this.type][this.identity].name;
        },
        set(value) {
          this.$store.dispatch('updatename', {identity: this.identity, type: this.type, value: value});
        }
      },
      date: {
        get() {
          return this.type;
        },
        set(value) {
          if(this.type !== value) {
            this.$store.dispatch('moveentry', {identity: this.identity, type: this.type, to: value});
            this.dialog = false;
          }
        }
      },
      spending: {
        get() {
          return this.$store.getters[this.type][this.identity].spending
        },
        set(value) {
          this.$store.dispatch('updatespending', {identity: this.identity, type: this.type, value: value});
        }
      },
      value: {
        get() {
          return this.$store.getters[this.type][this.identity].value;
        },
        set(value) {
          this.$store.dispatch('updatevalue', {identity: this.identity, type: this.type, value: value});
        }
      },
      steps: {
        get() {
          return this.$store.getters[this.type][this.identity].steps;
        },
        set(value) {
          this.$store.dispatch('updatesteps', {identity: this.identity, type: this.type, value: value});
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
      },
      typeshow(typename) {
        return this.translate(this.typename(typename)+(this.steps > 1 ? 's.' : '.'))
      }
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>