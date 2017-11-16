<template>
	<div class="chart tab-pane active" id="hour-inter-chart">
		<canvas id="lineInterHour" width="400" height="300"></canvas>
	</div>
</template>

<script>
	import Chart from 'chart.js';
	import chartOptions from '../mixins/chartOptions';

	export default {
		props: ['totalViews', 'interactionsHour'],

		mixins: [chartOptions],

		watch: {
			interactionsHour: function() {
				this.init();
			}
		},

		methods: {
			calculatePercentActivityByHour() {
				var percentInteractionsPerHour = [];
				var length = this.interactionsHour.length - 1;
				for (var i = 0; i <= length; i++) {
					percentInteractionsPerHour.push(Math.round((this.interactionsHour[i] / this.totalViews) * 100));
				}
				return percentInteractionsPerHour;
			},

			init() {
				var percentActivityByHour = this.calculatePercentActivityByHour();
				var lineInteractionsHour = $("#lineInterHour").get(0).getContext("2d");
	    	var lineInteractionsHourData = this.formatLineDataHour(percentActivityByHour);
	    	var lineChartInterHour = new Chart(lineInteractionsHour, {
	    		type: 'line',
	    		data: lineInteractionsHourData,
	    		options: this.lineChartOptions
	    	});
			},
      
      formatLineDataHour(dataSet) {
      	var labels = ['12am', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12pm', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'];
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