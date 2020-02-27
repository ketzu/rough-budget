<template>
    <v-card>
        <v-card-text>
            <v-container>
                <v-row justify="center">
                    <h3>Monthly Expenses</h3>
                </v-row>
            </v-container>
            <chart :height="200" :chart-data="data" :options="options"></chart>
        </v-card-text>
    </v-card>
</template>

<script>
    import Settings from '../settingsmixin'
    import {mapGetters} from 'vuex'
    import {Doughnut, mixins} from 'vue-chartjs'

    const chart = {
        extends: Doughnut,
        props: ['options'],
        mounted() {
            this.renderChart(this.chartData, this.options);
        },
        mixins: [mixins.reactiveProp]
    };

    export default {
        name: "ExpensesChart",
        components: {
            chart
        },
        computed: {
            ...mapGetters(['expense', 'multiplier']),
            options() {
                const self = this;
                return {
                    legend: {
                        display: true
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItems, data) {
                                if (tooltipItems.xLabel === 0) {
                                    return '';
                                }
                                return data.labels[tooltipItems.index] + ': ' + self.formatcurrency(data.datasets[0].data[tooltipItems.index]);
                            }
                        }
                    }
                }
            },
            data() {
                return {
                    labels: Object.keys(this.expense),
                    datasets: [
                        {
                            hoverBackgroundColor: 'hsl(202, 70.5%, 43.5%)',
                            backgroundColor: Object.keys(this.expense).map(e => 'hsl(354, '+ (Math.abs(this.stringHashCode(e)%20)+40)+'%, '+ (Math.abs(this.stringHashCode(e)%30)+35)+'%)'),
                            data: Object.keys(this.expense).map(e => this.expense[e].value*this.multiplier[this.expense[e].type])
                        }
                    ]
                }
            }
        },
        methods: {
            stringHashCode(s) {
                let hash = 0;
                if (s.length == 0) return hash;
                for (let i = 0; i < s.length; i++) {
                    hash = ((hash<<5)-hash) + s.charCodeAt(i);
                    hash = hash & hash;
                }
                return hash;
            }
        },
        mixins: [Settings]
    }
</script>

<style scoped>

</style>
