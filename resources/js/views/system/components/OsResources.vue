<template>
  <v-card class="pb-2" :loading="loading">
    <v-card-title>
      <v-row no-gutters align="center">
        <v-col>
          <div class="d-flex align-center">
            <span class="font-weight-medium">OS Info</span>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  small
                  class="ml-1"
                >
                  help
                </v-icon>
              </template>
              <span>
                Информация о системе
              </span>
            </v-tooltip>
          </div>
        </v-col>

        <v-col cols="auto" class="ml-4">
          <v-btn icon title="Обновить" @click="refresh">
            <v-icon>refresh</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-card-title>
    <v-card-text>
      <div class="v-data-table theme--light">
        <div class="v-data-table__wrapper">
            <table>
                <tbody>
                  <os-resources-list :data="data"/>
                </tbody>
            </table>
        </div>
    </div>
    </v-card-text>
    <div>
      <notifications group="foo" position="bottom center" class="mt-6" />
    </div>
  </v-card>
</template>

<script>
import OsResourcesList from './OsResourcesList.vue';
export default {
  name: "OsResources",

  components: {
    OsResourcesList
  },

  data() {
    return {
      loading: false,
      data: {},
    };
  },

  created() {
    this.fetchData();
  },

  methods: {
    fetchData() {
      this.loading = true;
      window.axios
        .get("system-info/os-resources")
        .then((response) => {
          this.data = response.data;
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
    },
  },
};
</script>
