<template>
  <v-list-item>
    <template #prepend>
      <v-btn
        :icon="spending ? 'fas fa-arrow-circle-down' : 'fas fa-arrow-circle-up'"
        :color="spending ? 'red-darken-2' : 'blue-darken-2'"
        size="large"
        @click="spending = !spending"
      ></v-btn>
    </template>

      <div class="entry-content" data-v-step="entry">
        <v-dialog v-model="dialog" max-width="600px">
          <template #activator="{ props }">
          <span v-bind="props">
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
                    <v-switch color="red-darken-2" :label="!spending ? 'Income' : 'Expense'" v-model="spending"></v-switch>
                  </v-col>
                  <v-col>
                    <v-select
                        :items="[1, 2, 3, 4, 5, 6, 7, 8]"
                        v-model="steps"
                        prefix="Every"
                    >
                    </v-select>
                  </v-col>
                  <v-col>
                    <v-select
                        :items="[
                          { title: typeshow('daily'), value: 'daily' },
                          { title: typeshow('weekly'), value: 'weekly' },
                          { title: typeshow('monthly'), value: 'monthly' },
                          { title: typeshow('yearly'), value: 'yearly' }
                        ]"
                        item-title="title"
                        item-value="value"
                        v-model="date"
                    >
                    </v-select>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue-darken-2" variant="text" @click="dialog = false">Close</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>

    <template #append>
      <div class="entry-actions">
          <v-btn icon="fas fa-chart-line" color="blue-darken-2" ripple @click="$store.dispatch('newtracking', {type: type, identity: identity})" data-v-step="track"></v-btn>
          <v-btn icon="fas fa-times" color="grey-darken-2" ripple @click="$store.dispatch('delentry',{type: type, identity: identity})" data-v-step="delete"></v-btn>
      </div>
    </template>
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
.entry-content {
  min-width: 0;
}

.entry-actions {
  align-items: center;
  display: flex;
  gap: 0.5rem;
  margin-inline-start: 1rem;
}
</style>
