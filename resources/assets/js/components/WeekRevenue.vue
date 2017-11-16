<template>
	<div class="chart tab-pane active" id="week-revenue-chart">
		<canvas id="barRevenueWeek" width="400" height="300"></canvas>
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
				topPostsRevenueWeek: [],
			}
		},

		created() {
			this.fetchWeekRevenue();
		},

		methods: {
			fetchWeekRevenue() {
				axios.get('/api/web/posts/analytics/' + this.profileSlug + '?revenueWeek=1&type=get')
          .then(this.setTopPostsWeek);
			},

			setTopPostsWeek({data}) {
				this.topPostsRevenueWeek = data;
				this.init();
			},

			init() {
				var barRevenueWeekFirst = $("#barRevenueWeek").get(0);
				var barRevenueWeek = barRevenueWeekFirst.getContext("2d");
				var barRevenueWeekData = this.formatBarData(this.topPostsRevenueWeek);
	    	var barChartRevenueWeek = new Chart(barRevenueWeek, {
	    		type: 'bar',
	    		data: barRevenueWeekData,
	    		options: this.barChartOptionsRevenue
	    	});
	    	this.attachShowPostModal(barRevenueWeekFirst, barChartRevenueWeek, this.topPostsRevenueWeek);
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

      attachShowPostModal(barRevenueWeekFirst, barChartRevenueWeek, topPostsWeek) {
      	barRevenueWeekFirst.onclick = function(evt) {
	    		var activePoints = barChartRevenueWeek.getElementsAtEvent(evt);
	    		var idx = activePoints[0]['_index'];
	    		var post = topPostsWeek[idx];
	    		VueEvent.fire('setSelectedPost', post);
	    		$('#showPost').modal('show');
	    	};
      }
		}
	}
</script>