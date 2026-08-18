<template>
  <v-card>
    <v-card-title>
      <h4>
        {{title}} ({{firstuppercase(type)}})
      </h4>

      <v-spacer></v-spacer>
      <div class="log-toolbar-actions">
        <v-btn icon="fas fa-cog" color="grey-darken-2" variant="text" @click="edit=!edit"></v-btn>
        <v-btn icon="fas fa-times" color="grey-darken-2" variant="text" @click="$store.dispatch('deltracking', index)"></v-btn>
      </div>
    </v-card-title>
    <v-card-text>
      <line-chart :data="data" :options="options" v-if="values.length>0"></line-chart>
      <v-divider v-if="edit"></v-divider>
      <v-list v-if="edit">
        <v-list-item lines="two" :key="index" v-for="(value,index) in rawvalues">
          <div>
            <v-list-item-title>
              {{(new Date(value.date)).toLocaleDateString()}}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{formatcurrency(value.value)}}
            </v-list-item-subtitle>
          </div>

          <template #append>
            <div class="log-entry-actions">
              <v-btn icon="fas fa-times" color="grey-darken-2" variant="text" ripple @click="removeEntry(value)"></v-btn>
            </div>
          </template>
        </v-list-item>
      </v-list>
      <v-divider></v-divider>
      <v-container style="margin-bottom: -40px;">
        <v-row>
          <v-col>
            <v-text-field v-model="newentry" @keyup.enter="addEntry()"
                          :append-icon="newentry != 0 ? 'fas fa-plus' : ''"
                          :prefix="currency"
                          prepend-icon="fas fa-money-bill-wave-alt"
                          @click:append="addEntry">
              <template #label>
                New data: Amount {{ spending ? "spent" : "earned" }}
              </template>
            </v-text-field>
          </v-col>
          <v-col>
            <v-dialog
                ref="dialog"
                v-model="modal"
                persistent
                width="290px"
            >
              <template #activator="{ props }">
                <v-text-field
                    v-bind="props"
                    :model-value="(new Date(newdate)).toLocaleDateString()"
                    :label="`When was the amount ${spending ? 'spent' : 'earned'}`"
                    prepend-icon="fas fa-calendar-alt"
                    readonly
                >
                </v-text-field>
              </template>
              <v-date-picker v-model="newdate" @update:model-value="modal = false">
              </v-date-picker>
            </v-dialog>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
    <v-card-actions v-if="values.length>0">
      <h3 class="ms-5">
        <span v-if="values.length>0">
          <span v-if="steps>1">{{steps}}-</span>
          {{firstuppercase(type)}} average: {{formatcurrency(averages.slice(-1)[0]*steps)}}
        </span>
      </h3>
      <v-spacer></v-spacer>
      <v-btn variant="text" @click="putback()">
        Use value
        <v-icon class="ms-2" size="small">fas fa-share-square</v-icon>
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
  import Settings from './settingsmixin'
  import {mapGetters} from 'vuex'
  import {Line} from 'vue-chartjs'
  import {CategoryScale, Chart as ChartJS, Legend, LinearScale, LineElement, PointElement, Tooltip} from 'chart.js'

  ChartJS.register(CategoryScale, Legend, LinearScale, LineElement, PointElement, Tooltip)

  const dateformat = (date) => {
    return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
  };

  const normalizeDate = (date) => date instanceof Date ? dateformat(date) : String(date).substring(0, 10);

  export default {
    name: 'tracking',
    props: ['index'],
    components: {
      'line-chart': Line
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
          date: normalizeDate(this.newdate)
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
          plugins: {
            tooltip: {
            callbacks: {
              label: function (tooltipItems) {
                return tooltipItems.dataset.label + ': ' + self.formatcurrency(tooltipItems.parsed.y);
              }
            }
            }
          },
          scales: {
            y: {
              ticks: {
                callback: function (label) {
                  return label + self.currency;
                }
              }
            },
            x: {
              ticks: {
                callback: function (label) {
                  const obj = new Date(label);
                  switch (self.type) {
                    case 'daily':
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
         let datevalue = value => normalizeDate(value.date);
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
.log-entry-actions {
  align-items: center;
  display: flex;
  margin-inline-start: 1rem;
}

.log-toolbar-actions {
  display: flex;
  gap: 0.5rem;
}
</style>
