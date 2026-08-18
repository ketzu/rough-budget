<template>
    <v-card>
        <v-card-text>
            <v-container>
                <v-row justify="center">
                    <h3>Monthly Expenses</h3>
                </v-row>
            </v-container>
            <div class="chart-container">
                <doughnut-chart :data="data" :options="options"></doughnut-chart>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
    import Settings from '../settingsmixin'
    import {mapGetters} from 'vuex'
    import {Doughnut} from 'vue-chartjs'
    import {ArcElement, Chart as ChartJS, Legend, Tooltip} from 'chart.js'

    ChartJS.register(ArcElement, Legend, Tooltip)

    export default {
        name: "ExpensesChart",
        components: {
            'doughnut-chart': Doughnut
        },
        computed: {
            ...mapGetters(['expense', 'multiplier']),
            options() {
                const self = this;
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                        callbacks: {
                            label: function (tooltipItems) {
                                if (tooltipItems.parsed === 0) {
                                    return '';
                                }
                                return tooltipItems.label + ': ' + self.formatcurrency(tooltipItems.parsed);
                            }
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
.chart-container {
    height: clamp(280px, 35vw, 440px);
    position: relative;
}

</style>
