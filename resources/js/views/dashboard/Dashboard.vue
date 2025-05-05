<template>
    <div>
        <div class="row">
            <div class="col-sm-6">
                <call-count-chart />
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <asterisk-commands-list />
            </div>
        </div>
    </div>
</template>

<script>
import CallCountChart from './components/CallCountChart.vue';
import AsteriskCommandsList from './components/AsteriskCommandsList.vue'
export default {
    name: 'Dashboard',

    components: {
        CallCountChart,
        AsteriskCommandsList
    }
}
</script>