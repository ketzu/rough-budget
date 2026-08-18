<template>
  <v-card>
    <v-card-text>
      <v-container>
        <v-row justify="center">
          <h3>Monthly Budged Overview</h3>
        </v-row>
      </v-container>
      <bar-chart :height="200" :data="data" :options="options"></bar-chart>
    </v-card-text>
  </v-card>
</template>

<script>
  import Settings from '../settingsmixin'
  import {mapGetters} from 'vuex'
  import {Bar} from 'vue-chartjs'
  import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Tooltip
  } from 'chart.js'

  ChartJS.register(BarElement, CategoryScale, Legend, LinearScale, Tooltip)

  export default {
    name: 'overview-chart',
    components: {
      'bar-chart': Bar
    },
    computed: {
      ...mapGetters(['incomes', 'expenses', 'multiplier', 'balance']),
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
          indexAxis: 'y',
          scales: {
            x: {
              stacked: true,
              ticks: {
                beginAtZero: true,
                callback: function (label) {
                  return label + self.currency;
                }
              },
            },
            y: {
              stacked: true
            }
          },
          plugins: {
            legend: {
            display: false
            },
            tooltip: {
              callbacks: {
                label: function (tooltipItems) {
                  if (tooltipItems.parsed.x === 0) {
                    return '';
                  }
                  return tooltipItems.dataset.label + ': ' + self.formatcurrency(tooltipItems.parsed.x);
                }
              }
            }
          }
        }
      },
      data() {
        return {
          labels: ['Incomes', 'Expenses'],
          datasets: [
            {
              barPercentage: 1.0,
              categoryPercentage: 1.0,
              label: 'Daily',
              backgroundColor: [
                'hsl(202, 52.4%, 28.6%)',
                'hsl(354, 70.5%, 33.5%)'
              ],
              data: this.daily
            },
            {
              barPercentage: 1.0,
              categoryPercentage: 1.0,
              label: 'Weekly',
              backgroundColor: [
                'hsl(202, 52.4%, 38.6%)',
                'hsl(354, 70.5%, 43.5%)'
              ],
              data: this.weekly
            },
            {
              barPercentage: 1.0,
              categoryPercentage: 1.0,
              label: 'Monthly',
              backgroundColor: [
                'hsl(202, 52.4%, 48.6%)',
                'hsl(354, 70.5%, 53.5%)'
              ],
              data: this.monthly
            },
            {
              barPercentage: 1.0,
              categoryPercentage: 1.0,
              label: 'Yearly',
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
    methods: {
      helper(type) {
        return [this.incomes[type] * this.multiplier[type], this.expenses[type] * this.multiplier[type]];
      }
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>
