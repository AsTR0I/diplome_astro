<template>
    <div>
        <v-row>
            <v-col cols="6">
                <system-resources />
            </v-col>
            <v-col>
                <system-hardware-resources />
            </v-col>
        </v-row>
    </div>
</template>

<script>
import SystemResources from './components/SystemResources.vue';
import SystemHardwareResources from './components/SystemHardwareResources.vue';

export default {
    name: 'SystemDashboard',

    components: {
        SystemResources,
        SystemHardwareResources
    }
}
</script>