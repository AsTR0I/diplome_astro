<template>
     <v-card
        class="pb-2"
        :loading="loading"
    >
        <v-card-title>
            <v-row no-gutters align="center">
                <v-col>
                    CDR (<span style="color: rgba(51, 102, 204, 0.5); font-size: medium;">{{chartData.total}}</span>)
                </v-col>

                <v-col cols="auto">
                    <v-btn-toggle
                        tile
                        dense
                        mandatory
                        borderless
                        color="primary"
                        v-model="period"
                    >
                        <v-btn
                        small
                        value="today"
                        >
                        Сегодня
                        </v-btn>
                            <v-btn
                        small
                        value="-1day"
                        >
                        Вчера
                        </v-btn>
                        <v-btn
                        small
                        value="month"
                        >
                        Месяц
                        </v-btn>
                        <v-btn
                        small
                        value="half-of-year"
                        >
                        Пол года
                        </v-btn>
                        <v-btn
                        small
                        value="all"
                        >
                        Все
                        </v-btn>
                    </v-btn-toggle>
                </v-col>

                <v-col cols="auto" class="ml-4">
                    <v-btn
                        icon
                        title="Обновить"
                        @click="refresh"
                    >
                        <v-icon>refresh</v-icon>
                    </v-btn>
                </v-col>
            </v-row>
        </v-card-title>
        <bar-chart 
            style="max-height: 250px"
            :options="options"
            :chart-data="chartData"
        />
        <div>
            <notifications group="foo" position="bottom center" class="mt-6"/>
        </div>
    </v-card>
</template>

<script>
import BarChart from '@/components/charts/BarChart';

const options = {
  responsive: true,
  maintainAspectRatio: false,
  legend: {
    position: 'bottom'
  },
  scales: {
    xAxes: [{
      gridLines: {
        display: false
      }
    }],
    yAxes: [{
      display: true,
      ticks: {
        beginAtZero: true
      }
    }]
  }
}

export default {
    name: 'CallCountChart',

    components: {
        BarChart
    },

    data() {
        return {
            loading: false,
            period: 'all',
            chartData: {
                labels: [],
                datasets: [],
                total: 0
            },
            options,
        }
    },

    created() {
        this.fetchData(this.period)
    },

    methods: {
        fetchData(period) {
            this.loading = true;
            window.axios.get('/calls/calls-count', { params: { period }})
                .then(response => {
                    this.chartData = response.data;
                })
                .catch((error) => {
                    this.$notify({
                        group: 'foo',
                        title: 'Ошибка',
                        text: 'Ошибка при загрузке данных "Кол-во звонков АТС"',
                        type: 'error',
                        position: 'bottom center'
                    });
                })
                .finally(() => {
                    this.loading = false;
                })
        },

        refresh() {
            this.fetchData(this.period);
        }
    },

    watch: {
        period(value) {
            this.fetchData(value);
        }
    },
}
</script>