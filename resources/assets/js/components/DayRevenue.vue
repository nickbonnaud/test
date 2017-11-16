<template>
	<div class="chart tab-pane" id="day-revenue-chart">
		<canvas id="lineRevenueDay" width="400" height="300"></canvas>
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
				postsPercentRevenueByDay: []
			}
		},

		watch: {
			fetchData: function() {
				if (this.fetchData) {
					this.getDayRevenueData();
				}
			}
		},

		methods: {

			getDayRevenueData() {
				axios.get('/api/web/analytics/posts/' + this.profileSlug + '?dayRevenue&type=get')
        	.then(this.setPostsPercentRevenueByDay);
			},

			setPostsPercentRevenueByDay({data}) {
				for (var i = 0; i <= 6; i++) {

					if (data.find(d => d.date == i)) {
						var day = data.find(d => d.date == i);
						this.postsPercentRevenueByDay.push(Math.round((day.total_revenue / this.totalRevenue) * 100));
					} else {
						this.postsPercentRevenueByDay.push(0);
					}
      	}
      	this.init();
			},

			init() {
				var lineRevenueDay = $("#lineRevenueDay").get(0).getContext("2d");
	    	var lineRevenueDayData = this.formatLineData(this.postsPercentRevenueByDay);
	    	var lineChartRevenue = new Chart(lineRevenueDay, {
	    		type: 'line',
	    		data: lineRevenueDayData,
	    		options: this.lineChartOptions
	    	});
			},
      
      formatLineData(dataSet) {
      	var labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
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