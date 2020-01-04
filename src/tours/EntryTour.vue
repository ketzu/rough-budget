<template>
    <v-tour :name="tourid" :steps="steps" :callbacks="callbacks"></v-tour>
</template>

<script>
    export default {
        name: "EntryTour",
        data () {
            return {
                opened: false,
                tourid: "EntryTour",
                steps: [
                    {
                        target: '[data-v-step="delete"]',
                        content: 'You can simply delete your new entry.'
                    },
                    {
                        target: '[data-v-step="track"]',
                        content: 'You can create a tracking entry if you are not sure how you should value it.'
                    },
                    {
                        target: '[data-v-step="entry"]',
                        content: 'Or you can click the new entry for details.'
                    }
                ],
                callbacks: {
                    onStop: this.onStop
                }
            }
        },
        methods: {
            onStop() {
                this.$store.dispatch('settourviewed', this.tourid);
            }
        },
        mounted: function () {
            // only open once
            if(!this.opened)
                this.opened = true;
            else
                return;

            if(this.$store.getters.tour[this.tourid] !== true)
                this.$tours[this.tourid].start()
        }
    }
</script>

<style scoped>

</style>
