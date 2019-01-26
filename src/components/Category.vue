<template>
  <v-card>
    <v-card-title class="pb-0">
      <h2>{{title}}</h2>
      <v-spacer></v-spacer>
      <v-btn icon @click="expand = !expand">
        <v-icon v-if="expand" large color="blue darken-2">fas fa-angle-up</v-icon>
        <v-icon v-else large color="blue darken-2">fas fa-angle-down</v-icon>
      </v-btn>
    </v-card-title>

    <v-list two-line v-if="expand">
      <budget-entry v-bind:key="key" :identity="key" :type="type"  v-for="(entry, key) in entries"></budget-entry>
      <v-list-tile>
        <v-text-field v-model="newname" @keyup.enter="addEntry()"
                      :append-icon="newname != '' ? 'fa-plus' : ''"
                      @click:append="addEntry">
          <template slot="label">
            {{translate("New entry")}}
          </template>
        </v-text-field>
      </v-list-tile>
    </v-list>

    <v-card-actions class="pt-0">
      <v-spacer></v-spacer>
      <h2>
        {{balance>0? "+" : "" }}{{formatcurrency(balance)}}
      </h2>
    </v-card-actions>
  </v-card>
</template>

<script>
  import Entry from './Entry.vue'
  import Settings from './settingsmixin'

  export default {
    name: 'category',
    components: {
      'budget-entry': Entry
    },
    props: [
      'type'
    ],
    computed: {
      entries() {
        return this.$store.getters[this.type];
      },
      balance() {
        return this.$store.getters.balances[this.type];
      },
      name() {
        return this.type;
      },
      title() {
        return this.firstuppercase(this.translate(this.type));
      }
    },
    data() {
      return {
        newname: '',
        expand: true,
        translation: {
          "de": {
            "daily": "tägliches",
            "weekly": "wöchentliches",
            "monthly": "monatliches",
            "yearly": "jährliches",
            "New entry": "Neuer Eintrag"
          }
        }
      }
    },
    methods: {
      addEntry() {
        if (this.newname in this.entries || this.newname === '') {
          // already exists or empty, do error stuff
        } else {
          this.$store.dispatch('newentry', {type: this.type, name: this.newname});
          this.newname = '';
        }
      }
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>