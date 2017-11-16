<template>
	<div class="chart tab-pane" id="hour-revenue-chart">
		<canvas id="lineRevenueHour" width="400" height="300"></canvas>
	</div>
</template>

<script>
	import Chart from 'chart.js';
	import chartOptions from '../mixins/chartOptions';

	export default {
		props: ['fetchData', 'profileSlug', 'totalRevenue'],

		mixins: [chartOptions],

		data() {
			return {
				postsPercentRevenueByHour: []
			}
		},

		watch: {
			fetchData: function() {
				if (this.fetchData) {
					this.getHourRevenueData();
				}
			}
		},

		methods: {

			getHourRevenueData() {
				axios.get('/api/web/analytics/posts/' + this.profileSlug + '?hourRevenue&type=get')
        	.then(this.setPostsPercentRevenueByHour);
			},

			setPostsPercentRevenueByHour({data}) {
				for (var i = 0; i <= 23; i++) {

					if (data.find(h => h.hour == i)) {
						var hour = data.find(h => h.hour == i)
						this.postsPercentRevenueByHour.push(Math.round((hour.total_revenue / this.totalRevenue) * 100));
					} else {
						this.postsPercentRevenueByHour.push(0);
					}
      	}
      	this.init();
			},

			init() {
				var lineRevenueHour = $("#lineRevenueHour").get(0).getContext("2d");
	    	var lineRevenueHourData = this.formatLineData(this.postsPercentRevenueByHour);
	    	var lineChartRevenueHour = new Chart(lineRevenueHour, {
	    		type: 'line',
	    		data: lineRevenueHourData,
	    		options: this.lineChartOptions
	    	});
			},
      
      formatLineData(dataSet) {
      	var labels = ['12am', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12pm', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'];
      	return {
					labels: labels,
					datasets: [
						{
							label: "% Revenue",
							fill: false,
							backgroundColor: "rgba(46, 204, 113,.4)",
							borderColor: "rgba(46, 204, 113,1.0)",
							borderCapStyle: "round",
							borderDash: [],
							borderDashOffset: 0.0,
							borderJoinStyle: 'bevel',
							pointBorderColor: "rgba(46, 204, 113,1.0)",
							pointBackgroundColor: "#fff",
							pointBorderWidth: 1,
          		pointHoverRadius: 5,
          		pointHoverBackgroundColor: "rgba(39, 174, 96,1.0)",
          		pointHoverBorderColor: "rgba(39, 174, 96,1.0)",
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