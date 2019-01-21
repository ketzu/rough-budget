<template>
  <v-card>
    <v-card-title>
      <h2>
        {{translate("Summary: You")}} {{translate(makelose)}}
        {{formatcurrency(Math.abs(balance))}} {{translate("monthly")}}.
      </h2>

      <v-spacer></v-spacer>
      <v-btn icon @click="expand = !expand">
        <v-icon v-if="expand" large color="blue darken-2">fas fa-angle-up</v-icon>
        <v-icon v-else large color="blue darken-2">fas fa-angle-down</v-icon>
      </v-btn>
    </v-card-title>
    <v-card-text v-if="expand">
      <v-layout row wrap>
        <v-flex md2>
          {{translate("Your free budget is")}}:
          <v-list>
            <v-list-tile v-for="item in ['daily', 'weekly', 'monthly', 'yearly']" :key="item.title">
              <v-list-tile-avatar>
                <v-icon v-if="balance < 0" color="red darken-2">fas {{icontype(item)}}</v-icon>
                <v-icon v-else color="blue darken-2">fas {{icontype(item)}}</v-icon>
              </v-list-tile-avatar>
              <v-list-tile-content>
                <v-list-tile-title>{{formatcurrency(Math.abs(balance/multiplier[item]))}} {{translate(item)}}.</v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-flex>
        <v-flex md4>
          {{translate("Your monthly budged consists of the following parts:")}}
          <chart :height="200" :chart-data="data" :options="options"></chart>
        </v-flex>
      </v-layout>
    </v-card-text>
  </v-card>
</template>

<script>
  import Settings from './settingsmixin'
  import {mapGetters} from 'vuex'
  import {HorizontalBar, mixins} from 'vue-chartjs'

  const chart = {
    extends: HorizontalBar,
    props: ['options'],
    mounted() {
      this.renderChart(this.chartData, this.options);
    },
    mixins: [mixins.reactiveProp]
  };

  export default {
    name: 'summary-chart',
    components: {
      chart
    },
    computed: {
      ...mapGetters(['incomes', 'expenses', 'multiplier', 'balance']),
      makelose() {
        return this.balance >= 0 ? 'are making' : 'are losing';
      },
      daily() {
        return this.helper('daily');
      },
      weekly() {
        return this.helper('weekly');
      },
      monthly() {
        return this.helper('monthly');
      },
      yearly() {
        return this.helper('yearly');
      },
      options() {
        const self = this;
        return {
          maintainAspectRatio: false,
          scales: {
            xAxes: [{
              stacked: true,
              ticks: {
                beginAtZero: true,
                callback: function (label, index, labels) {
                  return label + self.currency;
                }
              },
              categoryPercentage: 1.0
            }],
            yAxes: [{
              stacked: true,
              barPercentage: 1.0,
              categoryPercentage: 1.0
            }]
          },
          legend: {
            display: false
          },
          tooltips: {
            callbacks: {
              label: function (tooltipItems, data) {
                if (tooltipItems.xLabel === 0) {
                  return '';
                }
                return data.datasets[tooltipItems.datasetIndex].label + ': ' + self.formatcurrency(tooltipItems.xLabel);
              }
            }
          }
        }
      },
      data() {
        return {
          labels: [this.translate('Incomes'), this.translate('Expenses')],
          datasets: [
            {
              label: this.translate('Daily'),
              backgroundColor: [
                'hsl(202, 52.4%, 28.6%)',
                'hsl(354, 70.5%, 33.5%)'
              ],
              data: this.daily
            },
            {
              label: this.translate('Weekly'),
              backgroundColor: [
                'hsl(202, 52.4%, 38.6%)',
                'hsl(354, 70.5%, 43.5%)'
              ],
              data: this.weekly
            },
            {
              label: this.translate('Monthly'),
              backgroundColor: [
                'hsl(202, 52.4%, 48.6%)',
                'hsl(354, 70.5%, 53.5%)'
              ],
              data: this.monthly
            },
            {
              label: this.translate('Yearly'),
              backgroundColor: [
                'hsl(202, 52.4%, 58.6%)',
                'hsl(354, 70.5%, 63.5%)'
              ],
              data: this.yearly
            }
          ]
        }
      }
    },
    data() {
      return {
        expand: true,
        translation: {
          "de": {
            "Your monthly budged consists of the following parts:": "Ihr monatliches Budget setzt sich folgendermaßen zusammen:",
            "daily": "pro Tag",
            "weekly": "pro Woche",
            "monthly": "pro Monat",
            "yearly": "pro Jahr",
            "Summary: You": "Kurzfassung: Sie",
            "are making": "erhalten",
            "are losing": "verlieren",
            "Your free budget is": "Ihr verfügbares Budget entspricht",
            "Incomes": "Einnahmen",
            "Expenses": "Ausgaben",
            "Daily": "Täglich",
            "Weekly": "Wöchentlich",
            "Monthly": "Monatlich",
            "Yearly": "Jährlich"
          }
        }
      }
    },
    methods: {
      helper(type) {
        return [this.incomes[type] * this.multiplier[type], this.expenses[type] * this.multiplier[type]];
      },
      icontype(type) {
        switch (type) {
          case "daily": return "fa-calendar-day";
          case "weekly": return "fa-calendar-week";
          case "monthly": return "fa-calendar-alt";
          case "yearly": return "fa-calendar-check ";
        }
        return "";
      }
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>