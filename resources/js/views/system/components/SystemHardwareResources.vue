<template>
  <v-card class="pb-2" :loading="loading">
    <!-- Заголовок с кнопкой раскрытия -->
    <v-card-title @click="show = !show" class="d-flex justify-space-between align-center" style="cursor: pointer;">
          <div class="d-flex align-center">
            <span class="font-weight-medium">Hard drivers</span>
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
                Информация о жёстких дисках системы     
              </span>
            </v-tooltip>
          </div>
      
      <div class="d-flex align-center">
      <v-icon >{{ show ? 'keyboard_arrow_up' : 'keyboard_arrow_down' }}</v-icon>
      <v-btn icon title="Обновить" @click.stop="refresh">
          <v-icon>refresh</v-icon>
        </v-btn>
      </div>
      
    </v-card-title>

    <!-- Контент, скрывается/показывается -->
    <v-expand-transition>
      <div v-show="show">
        <v-card-text>
          <div class="v-data-table theme--light">
            <div class="v-data-table__wrapper">
              <table>
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Type</th>
                    <th>Mountpoint</th>
                    <th>Model</th>
                  </tr>
                </thead>
                <tbody>
                  <system-hardware-list-item
                    v-for="(item, index) in hardDriversData"
                    :data="item"
                    :key="index"
                  />
                </tbody>
              </table>
            </div>
          </div>
        </v-card-text>
      </div>
    </v-expand-transition>

    <notifications group="foo" position="bottom center" class="mt-6" />
  </v-card>
</template>

<script>
import SystemHardwareListItem from './SystemHardwareListItem.vue';

export default {
  name: "SystemHardwareResources",

  components: {
    SystemHardwareListItem,
  },

  data() {
    return {
      show: false,
      loading: false,
      hardDriversData: {},
    };
  },

  created() {
    this.fetchData();
  },

  methods: {
    fetchData() {
      this.loading = true;
      window.axios
        .get("system-info/hard-drivers-resources")
        .then((response) => {
          this.hardDriversData = response.data;
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
