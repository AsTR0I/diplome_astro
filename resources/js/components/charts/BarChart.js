import { Bar, mixins } from 'vue-chartjs'

const { reactiveProp } = mixins

export default {
  extends: Bar,

  props: ['chartData', 'options'],

  mounted () {
    this.renderChart(this.chartData, this.options)
  },

  mixins: [
    reactiveProp
  ]
}
