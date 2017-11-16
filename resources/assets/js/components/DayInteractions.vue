<template>
	<div class="chart tab-pane active" id="day-inter-chart">
		<canvas id="lineInterDay" width="400" height="300"></canvas>
	</div>
</template>

<script>
	import Chart from 'chart.js';
	import chartOptions from '../mixins/chartOptions';

	export default {
		props: ['totalViews', 'interactionsDay'],

		mixins: [chartOptions],

		watch: {
			interactionsDay: function() {
				this.init();
			}
		},

		methods: {
			calculatePercentActivityByDay() {
				var percentInteractionsPerDay = [];
				var length = this.interactionsDay.length - 1;
				for (var i = 0; i <= length; i++) {
					percentInteractionsPerDay.push(Math.round((this.interactionsDay[i] / this.totalViews) * 100));
				}
				return percentInteractionsPerDay;
			},

			init() {
				var percentActivityByDay = this.calculatePercentActivityByDay();
				var lineInteractionsDay = $("#lineInterDay").get(0).getContext("2d");
	    	var lineInteractionsDayData = this.formatLineData(percentActivityByDay);
	    	var lineChartInter = new Chart(lineInteractionsDay, {
	    		type: 'line',
	    		data: lineInteractionsDayData,
	    		options: this.lineChartOptions
	    	});
			},
      
      formatLineData(dataSet) {
      	var labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
      	return {
					labels: labels,
					datasets: [
						{
							label: "% Views, Shares, Bookmarks",
							fill: false,
							backgroundColor: "rgba(52, 152, 219,0.4)",
							borderColor: "rgba(52, 152, 219,1.0)",
							borderCapStyle: "round",
							borderDash: [],
							borderDashOffset: 0.0,
							borderJoinStyle: 'bevel',
							pointBorderColor: "rgba(52, 152, 219,1.0)",
							pointBackgroundColor: "#fff",
							pointBorderWidth: 1,
          		pointHoverRadius: 5,
          		pointHoverBackgroundColor: "rgba(41, 128, 185,1.0)",
          		pointHoverBorderColor: "rgba(41, 128, 185,1.0)",
          		pointHoverBorderWidth: 2,
          		pointRadius: 1,
          		data: dataSet,
          		spanGaps: false,
						}
					]
				}
      }
		}
	}
</script>