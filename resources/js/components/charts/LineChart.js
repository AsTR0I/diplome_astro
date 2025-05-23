import { Line, mixins } from 'vue-chartjs'

const { reactiveProp } = mixins

export default {
  extends: Line,

  props: ['chartData', 'options'],

  mounted () {
    this.renderChart(this.chartData, this.options)
  },

  mixins: [
    reactiveProp
  ]
}
