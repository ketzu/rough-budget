<template>
  <v-card>
    <v-card-title>
      <h4>
        {{title}} ({{firstuppercase(type)}})
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
      <v-divider v-if="edit"></v-divider>
      <v-list two-line v-if="edit">
        <v-list-item :key="index" v-for="(value,index) in rawvalues">
          <v-list-item-content>
            <v-list-item-title>
              {{(new Date(value.date)).toLocaleDateString()}}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{formatcurrency(value.value)}}
            </v-list-item-subtitle>
          </v-list-item-content>

          <v-list-item-action>
            <v-btn icon ripple @click="removeEntry(value)">
              <v-icon color="grey darken-2">fas fa-times</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>
      </v-list>
      <v-divider></v-divider>
        <v-dialog
            ref="dialog"
            v-model="modal"
            persistent
            width="290px"
        >
          <template v-slot:activator="{ on }">
            <v-text-field
                slot="activator"
                v-on="on"
                :value="(new Date(newdate)).toLocaleDateString()"
                prepend-icon="fa-calendar-alt"
                readonly
            ></v-text-field>
          </template>
          <v-date-picker v-model="newdate" scrollable @input="modal = false">
          </v-date-picker>
        </v-dialog>
        <v-text-field v-model="newentry" @keyup.enter="addEntry()"
                      :append-icon="newentry != 0 ? 'fa-plus' : ''"
                      :prefix="currency"
                      @click:append="addEntry">
          <template slot="label">
            Amount {{ spending ? "spent" : "earned" }}
          </template>
        </v-text-field>
    </v-card-text>
    <v-card-actions>
      <h3>
        <span v-if="values.length>0">
          <span v-if="steps>1">{{steps}}-</span>
          {{firstuppercase(type)}} average: {{formatcurrency(averages.slice(-1)[0]*steps)}}
        </span>
      </h3>
      <v-spacer></v-spacer>
      <v-btn text @click="putback()">
        Use value
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
  };

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
        newdate: dateformat(new Date())
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
      spending() {
        return this.$store.getters[this.type][this.name].spending;
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
                  callback: function (label) {
                    return label + self.currency;
                  }
                }
              }
            ],
            xAxes: [
              {
                ticks: {
                  callback: function (label) {
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
              label: 'Your Data',
              showLine: false,
              borderColor: 'hsl(210, 50%, 50%)',
              backgroundColor: 'hsl(210, 50%, 50%)',
              fill: false,
              data: this.datapoints // this.rawvalues.map(value => ({x: value.date, y: value.value}))
            },
            {
              label: 'Current Budget Plan',
              fill: false,
              borderColor: 'hsl(0, 50%, 50%)',
              backgroundColor: 'hsl(0, 50%, 50%)',
              data: Array(this.times.length).fill(this.value)
            },
            {
              label: this.firstuppercase('average'),
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