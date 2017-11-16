<template>
	<div class="chart tab-pane" id="month-revenue-chart">
		<canvas id="barRevenueMonth" width="400" height="300"></canvas>
	</div>
</template>

<script>
	import Chart from 'chart.js';
	import chartOptions from '../mixins/chartOptions';

	export default {
		props: ['profileSlug'],

		mixins: [chartOptions],

		data() {
			return {
				topPostsRevenueMonth: [],
			}
		},

		mounted() {
			VueEvent.listen('getMonthRevenueData', this.fetchMonthRevenue.bind(this));
		},

		methods: {
			fetchMonthRevenue() {
				if (this.topPostsRevenueMonth.length == 0) {
					axios.get('/api/web/posts/analytics/' + this.profileSlug + '?revenueMonth=1&type=get')
	          .then(this.setTopPostsMonth);
	      }
			},

			setTopPostsMonth({data}) {
				this.topPostsRevenueMonth = data;
				this.init()
			},

			init() {
				var barRevenueMonthFirst = $("#barRevenueMonth").get(0);
				var barRevenueMonth = barRevenueMonthFirst.getContext("2d");
				var barRevenueMonthData = this.formatBarData(this.topPostsRevenueMonth);
	    	var barChartRevenueMonth = new Chart(barRevenueMonth, {
	    		type: 'bar',
	    		data: barRevenueMonthData,
	    		options: this.barChartOptionsRevenue
	    	});
	    	this.attachShowPostModal(barRevenueMonthFirst, barChartRevenueMonth, this.topPostsRevenueMonth);
			},
      
      formatBarData(dataSet) {
      	var labels = [];
      	var data = [];
      	dataSet.forEach(function(post) {
      		if (post.message.length > 10) var message = post.message.substring(0, 10) + '...';
      		labels.push(message);
      		data.push((post.total_revenue / 100).toFixed(2));
      	});
      	return {
					labels: labels,
					datasets: [
						{
							label: "Revenue Per Post",
							backgroundColor: "rgba(46, 204, 113,.8)",
							hoverBorderColor: "rgba(39, 174, 96,1.0)",
          		data: data
						}
					]
				}
      },

      attachShowPostModal(barRevenueMonthFirst, barChartRevenueMonth, topPostsMonth) {
      	barRevenueMonthFirst.onclick = function(evt) {
	    		var activePoints = barChartRevenueMonth.getElementsAtEvent(evt);
	    		var idx = activePoints[0]['_index'];
	    		var post = topPostsMonth[idx];
	    		VueEvent.fire('setSelectedPost', post);
	    		$('#showPost').modal('show');
	    	};
      }
		}
	}
</script>