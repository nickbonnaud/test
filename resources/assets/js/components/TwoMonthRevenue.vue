<template>
	<div class="chart tab-pane" id="two-month-revenue-chart">
		<canvas id="barRevenueTwoMonth" width="400" height="300"></canvas>
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
				topPostsRevenueTwoMonth: [],
			}
		},

		mounted() {
			VueEvent.listen('getTwoMonthRevenueData', this.fetchTwoMonthRevenue.bind(this));
		},

		methods: {
			fetchTwoMonthRevenue() {
				if (this.topPostsRevenueTwoMonth.length == 0) {
					axios.get('/api/web/posts/analytics/' + this.profileSlug + '?revenueTwoMonth=1&type=get')
	          .then(this.setTopPostsTwoMonth);
	      }
			},

			setTopPostsTwoMonth({data}) {
				this.topPostsRevenueTwoMonth = data;
				this.init()
			},

			init() {
				var barRevenueTwoMonthFirst = $("#barRevenueTwoMonth").get(0);
				var barRevenueTwoMonth = barRevenueTwoMonthFirst.getContext("2d");
				var barRevenueTwoMonthData = this.formatBarData(this.topPostsRevenueTwoMonth);
	    	var barChartRevenueTwoMonth = new Chart(barRevenueTwoMonth, {
	    		type: 'bar',
	    		data: barRevenueTwoMonthData,
	    		options: this.barChartOptionsRevenue
	    	});
	    	this.attachShowPostModal(barRevenueTwoMonthFirst, barChartRevenueTwoMonth, this.topPostsRevenueTwoMonth);
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

      attachShowPostModal(barRevenueTwoMonthFirst, barChartRevenueTwoMonth, topPostsTwoMonth) {
      	barRevenueTwoMonthFirst.onclick = function(evt) {
	    		var activePoints = barChartRevenueTwoMonth.getElementsAtEvent(evt);
	    		var idx = activePoints[0]['_index'];
	    		var post = topPostsTwoMonth[idx];
	    		VueEvent.fire('setSelectedPost', post);
	    		$('#showPost').modal('show');
	    	};
      }
		}
	}
</script>