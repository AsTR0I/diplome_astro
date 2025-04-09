<template>
  <div>
    <doughnut-chart :chart-data="chartData" :options="options" />
  </div>
</template>

<script>
import { Doughnut, mixins } from "vue-chartjs";
const { reactiveProp } = mixins;

export default {
  name: "SystemRamChart",
  components: {
    DoughnutChart: {
      extends: Doughnut,
      mixins: [reactiveProp],
      props: ["chartData", "options"],
      mounted() {
        this.renderChart(this.chartData, this.options);
      },
    },
  },
  data() {
    return {
      chartData: {
        labels: [],
        datasets: [],
      },
      options: {
        responsive: true,
        maintainAspectRatio: 1.2,
        legend: {
          position: "bottom",
        },
        title: {
          display: true,
          text: "RAM",
          padding: 0,
        },
        layout: {
          padding: {
            left: 0,
            right: 0,
            top: 0,
            bottom: 0,
          },
        },
      },
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      window.axios
        .get("system-info/ram-chart")
        .then((response) => {
          this.chartData = response.data;
        })
        .catch(() => {
          this.$notify({
            group: "foo",
            title: "Ошибка",
            text: 'Ошибка при загрузке данных "RAM Chart"',
            type: "error",
            position: "bottom center",
          });
        });
    },
    refresh() {
      this.fetchData();
    }
  },
};
</script>
