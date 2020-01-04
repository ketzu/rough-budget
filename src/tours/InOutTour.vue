<template>
    <v-tour :name="tourid" :steps="steps" :callbacks="callbacks"></v-tour>
</template>

<script>
    export default {
        name: "InOutTour",
        data () {
            return {
                tourid: "InOutTour",
                steps: [
                    {
                        target: '[data-v-step="income"]',
                        content: 'Add a new income by giving it a name!'
                    },
                    {
                        target: '[data-v-step="income"]',
                        content: 'You can add the entry by pressing enter or pushing the plus button!'
                    }
                ],
                callbacks: {
                    onStop: this.onStop,
                    onNextStep: this.onNextStep
                }
            }
        },
        methods: {
            onStop() {
                this.$store.dispatch('settourviewed', this.tourid);
                document.querySelector('[data-v-step="income"]').focus();
            },
            onNextStep(currentStep) {
                if(currentStep===0) {
                    document.querySelector('[data-v-step="income"]').focus();
                }
            }
        },
        mounted: function () {
            if(this.$store.getters.tour[this.tourid] !== true)
                this.$tours[this.tourid].start()
        }
    }
</script>

<style scoped>

</style>
