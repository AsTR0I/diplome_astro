<template>
  <v-card class="pb-2" :loading="loading">
    <v-card-title>
      <v-row no-gutters align="center">
        <v-col>System Resources</v-col>

        <v-col cols="auto" class="ml-4">
          <v-btn icon title="Обновить" @click="refresh">
            <v-icon>refresh</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-card-title>
    <v-card-text>
      <v-row align="center">
        <v-col cols="6">
          <system-cpu-chart ref="systemCpuChart"/>
        </v-col>
        <v-col cols="6">
          <system-ram-chart ref="systemRamChart" />
        </v-col>
      </v-row>
      <v-row align="center">
        <v-col cols=12>
          <system-resources-list :data="systemData" />
        </v-col>
      </v-row>
    </v-card-text>
    <div>
      <notifications group="foo" position="bottom center" class="mt-6" />
    </div>
  </v-card>
</template>

<script>
import SystemResourcesList from "./SystemResourcesList.vue";
import SystemCpuChart from "./SystemCpuChart.vue";
import SystemRamChart from "./SystemRamChart.vue";

export default {
  name: "SystemResources",

  components: {
    SystemResourcesList,
    SystemCpuChart,
    SystemRamChart
  },

  data() {
    return {
      loading: false,
      systemData: {},
    };
  },

  created() {
    this.fetchData();
  },

  methods: {
    fetchData() {
      this.loading = true;
      window.axios
        .get("system-info/resources")
        .then((response) => {
          this.systemData = response.data;
        })
        .catch((error) => {
          this.$notify({
            group: "foo",
            title: "Ошибка",
            text: "Ошибка при загрузке данных",
            type: "error",
            position: "bottom center",
          });
        })
        .finally(() => {
          this.loading = false;
        });
    },

    refresh() {
      this.fetchData();
      this.$refs.systemCpuChart.refresh();
      this.$refs.systemRamChart.refresh();
    },
  },
};
</script>
