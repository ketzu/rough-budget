<template>
  <v-list-item>
    <v-list-item-avatar>
      <v-btn icon @click="spending = !spending">
        <v-icon large v-if="!spending" color="blue darken-2">fas fa-arrow-circle-up</v-icon>
        <v-icon large v-if="spending" color="red darken-2">fas fa-arrow-circle-down</v-icon>
      </v-btn>
    </v-list-item-avatar>

      <v-list-item-content data-v-step="entry">
        <v-dialog v-model="dialog" max-width="600px">
          <template v-slot:activator="{ on }">
          <span v-on="on">
            <v-list-item-title>
              {{name}}: {{value}}{{currency}}
            </v-list-item-title>
            <v-list-item-subtitle>
              Every {{steps>1 ? steps : ""}} {{typeshow(type)}}
            </v-list-item-subtitle>
          </span>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{name}}</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col>
                    <v-text-field label="Name" v-model="name" required></v-text-field>
                  </v-col>
                  <v-col>
                    <v-text-field label="Amount" :prefix="currency" v-model="value" required></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col>
                    <v-switch color="red darken-2" :label="!spending ? 'Income' : 'Expense'" v-model="spending"></v-switch>
                  </v-col>
                  <v-col>
                    <v-select
                        :items="[1, 2, 3, 4, 5, 6, 7, 8]"
                        v-model="steps"
                        prefix="Every"
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
                  </v-col>
                  <v-col>
                    <v-select
                        :items="['daily', 'weekly', 'monthly', 'yearly']"
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
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-2" text @click="dialog = false">Close</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-list-item-content>

    <v-list-item-action>
      <v-row>
          <v-btn icon ripple @click="$store.dispatch('newtracking', {type: type, identity: identity})" data-v-step="track">
            <v-icon color="blue darken-2">fas fa-chart-line</v-icon>
          </v-btn>
          <v-btn icon ripple @click="$store.dispatch('delentry',{type: type, identity: identity})" data-v-step="delete">
            <v-icon color="grey darken-2">fas fa-times</v-icon>
          </v-btn>
      </v-row>
    </v-list-item-action>
    <entry-tour></entry-tour>
  </v-list-item>
</template>

<script>
  import Settings from './settingsmixin'
  import EntryTour from "../tours/EntryTour";

  export default {
    name: 'entry',
    components: {EntryTour},
    props: ['identity', 'type'],
    data() {
      return {
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
      typeshow(typename) {
        return this.typename(typename)+(this.steps > 1 ? 's.' : '.');
      }
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>
