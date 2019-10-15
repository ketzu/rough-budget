<template>
  <v-card>
    <v-card-text>
      Your monthly budged consists of the following parts:
      <chart :height="200" :chart-data="data" :options="options"></chart>
    </v-card-text>
  </v-card>
</template>

<script>
  import Settings from '../settingsmixin'
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
    name: 'overview-chart',
    components: {
      chart
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
          scales: {
            xAxes: [{
              stacked: true,
              ticks: {
                beginAtZero: true,
                callback: function (label) {
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
          labels: ['Incomes', 'Expenses'],
          datasets: [
            {
              label: 'Daily',
              backgroundColor: [
                'hsl(202, 52.4%, 28.6%)',
                'hsl(354, 70.5%, 33.5%)'
              ],
              data: this.daily
            },
            {
              label: 'Weekly',
              backgroundColor: [
                'hsl(202, 52.4%, 38.6%)',
                'hsl(354, 70.5%, 43.5%)'
              ],
              data: this.weekly
            },
            {
              label: 'Monthly',
              backgroundColor: [
                'hsl(202, 52.4%, 48.6%)',
                'hsl(354, 70.5%, 53.5%)'
              ],
              data: this.monthly
            },
            {
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