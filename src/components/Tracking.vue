<template>
  <v-card>
    <v-card-title>
      <h4>
        {{title}} ({{translate(firstuppercase(type))}})
      </h4>

      <v-spacer></v-spacer>
      <v-btn icon @click="edit=!edit">
        <v-icon color="grey darken-2">fas fa-cog</v-icon>
      </v-btn>
      <v-btn icon @click="$store.dispatch('deltracking', index)">
        <v-icon color="grey darken-2">fas fa-times</v-icon>
      </v-btn>
    </v-card-title>
    <v-card-text>
      <chart :chart-data="data" :options="options" v-if="values.length>0"></chart>
      <v-list two-line v-if="edit">
        <v-list-tile :key="index" v-for="(value,index) in rawvalues">
          <v-list-tile-content>
            <v-list-tile-title>
              {{(new Date(value.date)).toLocaleDateString()}}
            </v-list-tile-title>
            <v-list-tile-sub-title>
              {{formatcurrency(value.value)}}
            </v-list-tile-sub-title>
          </v-list-tile-content>

          <v-list-tile-action>
            <v-btn icon ripple @click="removeEntry(value)">
              <v-icon color="grey darken-2">fas fa-times</v-icon>
            </v-btn>
          </v-list-tile-action>
        </v-list-tile>
      </v-list>
        <v-dialog
            ref="dialog"
            v-model="modal"
            persistent
            lazy
            full-width
            width="290px"
        >
          <v-text-field
              slot="activator"
              :value="(new Date(newdate)).toLocaleDateString()"
              prepend-icon="fa-calendar-alt"
              readonly
          ></v-text-field>
          <v-date-picker v-model="newdate" scrollable>
            <v-spacer></v-spacer>
            <v-btn flat color="primary" @click="modal = false">Cancel</v-btn>
            <v-btn flat color="primary" @click="$refs.dialog.save(date)">OK</v-btn>
          </v-date-picker>
        </v-dialog>
        <v-text-field v-model="newentry" @keyup.enter="addEntry()"
                      :append-icon="newentry != 0 ? 'fa-plus' : ''"
                      :prefix="currency"
                      @click:append="addEntry">
          <template slot="label">
            {{translate("New tracking data")}}
          </template>
        </v-text-field>
    </v-card-text>
    <v-card-actions>
      <h3>
        <span v-if="values.length>0">
          <span v-if="steps>1">{{steps}}-</span>
          {{translate(firstuppercase(type))}} {{translate("average")}}: {{formatcurrency(averages.slice(-1)[0]/steps)}}
        </span>
      </h3>
      <v-spacer></v-spacer>
      <v-btn flat @click="putback()">
        {{translate("Use value")}}
        <v-icon right small>fas fa-share-square</v-icon>
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
  import Settings from './settingsmixin'
  import {mapGetters} from 'vuex'
  import {Line, mixins} from 'vue-chartjs'

  const dateformat = (date) => {
    return date.getFullYear() + '-' + (date.getMonth() + 1).toLocaleString(undefined, {minimumIntegerDigits: 2}) + '-' + date.getDate();
  }

  let chart = {
    extends: Line,
    props: ['options'],
    mounted() {
      this.renderChart(this.chartData, this.options);
    },
    mixins: [mixins.reactiveProp]
  };

  export default {
    name: 'tracking',
    props: ['index'],
    components: {
      chart
    },
    data() {
      return {
        modal: false,
        newentry: 0,
        edit: false,
        newdate: dateformat(new Date()),
        translation: {
          "de": {
            "average": "Durchschnitt",
            "Entries": "Einträge",
            "Current Budget": "Derzeitiger Plan",
            "Daily": "Täglich",
            "Weekly": "Wöchentlich",
            "Monthly": "Monatlich",
            "Yearly": "Jährlich",
            "daily": "Täglicher",
            "weekly": "Wöchentlicher",
            "monthly": "Monatlicher",
            "yearly": "Jährlicher",
            "Use value": "Wert verwenden",
            "New tracking data": "Neuer Datenpunkt"
          }
        }
      };
    },
    methods: {
      addEntry() {
        this.$store.dispatch('addtrackentry', {
          identity: this.name,
          type: this.type,
          value: Number(this.newentry),
          date: this.newdate
        });
        this.newentry = 0;
      },
      removeEntry(value) {
        this.$store.dispatch('removetrackentry', {
          identity: this.name,
          type: this.type,
          value: value.value,
          date: value.date
        });
        this.newentry = 0;
      },
      putback() {
        this.$store.dispatch('updatevalue', {
          identity: this.name,
          type: this.type,
          value: Math.round(Number(this.average / this.steps))
        });
      }
    },
    computed: {
      ...mapGetters(['window']),
      name() {
        return this.$store.getters.trackings[this.index].name;
      },
      average() {
        return this.averages.slice(-1)[0] / this.steps;
      },
      value: {
        get() {
          return this.$store.getters[this.type][this.name].value;
        },
        set(value) {
          this.$store.dispatch('updatevalue', {identity: this.name, type: this.type, value: value});
        }
      },
      type() {
        return this.$store.getters.trackings[this.index].type;
      },
      steps() {
        return this.$store.getters[this.type][this.name].steps;
      },
      title() {
        return this.firstuppercase(this.name);
      },
      options() {
        const self = this;
        return {
          tooltips: {
            callbacks: {
              label: function (tooltipItems, data) {
                return data.datasets[tooltipItems.datasetIndex].label + ': ' + self.formatcurrency(tooltipItems.yLabel);
              }
            }
          },
          scales: {
            yAxes: [
              {
                ticks: {
                  callback: function (label, index, labels) {
                    return label + self.currency;
                  }
                }
              }
            ],
            xAxes: [
              {
                ticks: {
                  callback: function (label, index, labels) {
                    const obj = new Date(label);
                    switch (self.type) {
                      case 'daily':
                        return obj.getDate() + '. ' + obj.toLocaleString(undefined, {month: 'short'});
                      case 'weekly':
                        return obj.getDate() + '. ' + obj.toLocaleString(undefined, {month: 'short'});
                      case 'monthly':
                        return obj.toLocaleString(undefined, {month: 'long'});
                      case 'yearly':
                        return label;
                    }
                  }
                }
              }
            ]
          }
        }
      },
      data() {
        return {
          labels: this.times,
          datasets: [
            {
              label: this.translate('Entries'),
              showLine: false,
              borderColor: 'hsl(210, 50%, 50%)',
              backgroundColor: 'hsl(210, 50%, 50%)',
              fill: false,
              data: this.datapoints // this.rawvalues.map(value => ({x: value.date, y: value.value}))
            },
            {
              label: this.translate('Current Budget'),
              fill: false,
              borderColor: 'hsl(0, 50%, 50%)',
              backgroundColor: 'hsl(0, 50%, 50%)',
              data: Array(this.times.length).fill(this.value)
            },
            {
              label: this.firstuppercase(this.translate('average')),
              borderColor: 'hsl(50, 50%, 50%)',
              backgroundColor: 'hsl(50, 50%, 50%)',
              fill: false,
              data: this.averages
            }
          ]
        }
      },
      rawvalues() {
        return this.$store.getters.trackings[this.index].values;
      },
      values() {
        let container = {};
        let datevalue = value => value.date;
        switch (this.type) {
          case 'monthly':
            datevalue = value => value.date.substring(0, 7);
            break;
          case 'yearly':
            datevalue = value => value.date.substring(0, 4);
            break;
        }
        this.rawvalues.forEach((value) => {
          if (datevalue(value) in container) {
            container[datevalue(value)] += value.value;
          } else {
            container[datevalue(value)] = value.value;
          }
        });
        const result = [];
        Object.keys(container).sort().forEach(value => {
          result.push({date: value, value: container[value]});
        });
        return result;
      },
      times() {
        return this.values.map(value => value.date);
      },
      averages() {
        let avgs = [];
        let old;
        const window = this.window;
        const ring = [];
        this.values.forEach(value => {
          if (old !== undefined) {
            let diff = 0;
            let tdate = new Date(value.date);
            let ydiff = Math.max(tdate.getFullYear() - old.getFullYear() - 1, 0);
            let mdiff = Math.max(tdate.getMonth() - old.getMonth() - 1, 0);
            switch (this.type) {
              case 'daily':
                diff = ((tdate - old) / (1000 * 60 * 60 * 24)) - 1;
                break;
              case 'weekly':
                break;
              case 'monthly':
                diff = 12 * ydiff + mdiff;
                break;
              case 'yearly':
                diff = ydiff;
                break;
            }
            Array.prototype.push.apply(ring, (new Array(diff)).fill(0));
          }
          old = new Date(value.date);
          ring.push(value.value);
          ring.splice(0, ring.length - window);
          avgs.push(ring.reduce((p, c) => p + c) / (ring.length));
        });
        if (this.type === 'weekly') {
          avgs = avgs.map(v => v * 7);
        }
        return avgs;
      },
      datapoints() {
        if (this.values.length === 0) {
          return [];
        }
        return this.values.map(value => value.value);
      }
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>