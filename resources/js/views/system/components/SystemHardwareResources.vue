<template>
  <v-card class="pb-2" :loading="loading">
    <v-card-title>
      <v-row no-gutters align="center">
        <v-col>Hard drivers</v-col>

        <v-col cols="auto" class="ml-4">
          <v-btn icon title="Обновить" @click="refresh">
            <v-icon>refresh</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-card-title>
    <v-card-text>
    </v-card-text>
    <div>
      <notifications group="foo" position="bottom center" class="mt-6" />
    </div>
  </v-card>
</template>

<script>

export default {
  name: "SystemResources",

  components: {
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
