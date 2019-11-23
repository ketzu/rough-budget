<template>
  <v-card>
    <v-card-title class="pb-0">
      <h2>{{title}}</h2>
    </v-card-title>

    <v-list two-line>
      <budget-entry v-bind:key="key" :identity="key" :type="type_distinction(entry)"  v-for="(entry, key) in entries"></budget-entry>
      <v-list-item>
        <v-text-field v-model="newname" @keyup.enter="addEntry()"
                      :append-icon="newname != '' ? 'fa-plus' : ''"
                      @click:append="addEntry"
                      @focus="newEntryFocus=true"
                      @blur="newEntryFocus=false"
        >
          <template slot="label">
            {{newEntryText}}
          </template>
        </v-text-field>
      </v-list-item>
    </v-list>

    <v-card-actions class="pt-0">
      <v-spacer></v-spacer>
      <h2>
        {{balance>0? "+" : "" }}{{formatcurrency(balance)}} {{balanceadd}}
      </h2>
    </v-card-actions>
  </v-card>
</template>

<script>
  import Entry from './Entry.vue'
  import Settings from './settingsmixin'
  import {mapGetters} from "vuex";

  export default {
    name: 'category',
    components: {
      'budget-entry': Entry
    },
    props: [
      'type'
    ],
    computed: {
      ...mapGetters(['multiplier']),
      newEntryText() {
        if(!this.newEntryFocus && this.newname == ''){
          return "New Entry";
        }
        return "Name of new entry";
      },
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
        if(this.type === "income" || this.type === "expense")
          return this.firstuppercase(this.type+"s");
        return this.firstuppercase(this.type);
      },
      balanceadd() {
        if(this.type === "income" || this.type === "expense")
          return  "per month";
        if(this.type === "daily" ||this.type === "weekly" || this.type === "yearly")
          return "("+this.formatcurrency(this.balance*this.multiplier[this.type])+" per month)";
        return "";
      }
    },
    data() {
      return {
        newname: '',
        newEntryFocus: false
      }
    },
    methods: {
      addEntry() {
        if (this.newname in this.entries || this.newname === '') {
          // already exists or empty, do error stuff
        } else {
          if(this.type === "income" || this.type === "expense") {
            // need another exists check in this case, as data model sucks for income/expense mode
            if(this.newname in this.$store.getters["expense"]){
              return;
            }
            // Use monthly as default entry
            this.$store.dispatch('newentry', {type: "monthly", name: this.newname, spending: this.type==="expense"});
          }else{
            // use expense as default
            this.$store.dispatch('newentry', {type: this.type, name: this.newname, spending: true});
          }
          this.newname = '';
        }
      },
      type_distinction(entry) {
        if(this.type === "daily" || this.type === "weekly" || this.type === "monthly" || this.type === "yearly")
          return this.type;
        return entry.type;
      }
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>