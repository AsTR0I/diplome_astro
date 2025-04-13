<template>
    <div>
        <div class="row">
            <div class="col-sm-6">
                <call-count-chart />
            </div>
        </div>
    </div>
</template>

<script>
import CallCountChart from './components/CallCountChart.vue';

export default {
    name: 'Dashboard',

    components: {
        CallCountChart,
    }
}
</script>