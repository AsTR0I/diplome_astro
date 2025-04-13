<template>
    <div>
        <v-row>
            <v-col cols="6">
                <system-resources />
            </v-col>
            <v-col cols="6">
                <v-row>
                    <v-col cols="12">
                        <os-resources />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12">
                        <system-hardware-resources />
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import SystemResources from './components/SystemResources.vue';
import SystemHardwareResources from './components/SystemHardwareResources.vue';
import OsResources from './components/OsResources.vue';

export default {
    name: 'SystemDashboard',

    components: {
        SystemResources,
        SystemHardwareResources,
        OsResources
    }
}
</script>