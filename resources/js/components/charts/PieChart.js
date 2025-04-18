import { Pie, mixins } from 'vue-chartjs'

const { reactiveProp } = mixins

export default {
  extends: Pie,

  props: ['chartData', 'options'],

  mounted () {
    this.renderChart(this.chartData, this.options)
  },

  mixins: [
    reactiveProp
  ]
}
