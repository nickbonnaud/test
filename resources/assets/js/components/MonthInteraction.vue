<template>
	<div class="chart tab-pane" id="month-inter-chart">
		<canvas id="barInteractionsMonth" width="400" height="300"></canvas>
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
				topPostsInteractionsMonth: [],
			}
		},

		mounted() {
			VueEvent.listen('getMonthInterData', this.fetchMonthInteractions.bind(this));
		},

		methods: {
			fetchMonthInteractions() {
				if (this.topPostsInteractionsMonth.length == 0) {
					axios.get('/api/web/posts/analytics/' + this.profileSlug + '?interactionsMonth=1&type=get')
	          .then(this.setTopPostsMonth);
	      }
			},

			setTopPostsMonth({data}) {
				this.topPostsInteractionsMonth = data;
				this.init();
			},

			init() {
				var barInteractionsMonthFirst = $("#barInteractionsMonth").get(0);
				var barInteractionsMonth = barInteractionsMonthFirst.getContext("2d");
				var barInteractionsMonthData = this.formatBarData(this.topPostsInteractionsMonth);
	    	var barChartInterMonth = new Chart(barInteractionsMonth, {
	    		type: 'bar',
	    		data: barInteractionsMonthData,
	    		options: this.barChartOptionsInteractions
	    	});
	    	this.attachShowPostModal(barInteractionsMonthFirst, barChartInterMonth, this.topPostsInteractionsMonth);
			},
      
      formatBarData(dataSet) {
      	var labels = [];
      	var data = [];
      	dataSet.forEach(function(post) {
      		if (post.message.length > 10) var message = post.message.substring(0, 10) + '...';
      		labels.push(message);
      		data.push(post.total_interactions);
      	});
      	return {
					labels: labels,
					datasets: [
						{
							label: "Views, Shares, Bookmarks",
							backgroundColor: "rgba(52, 152, 219,.8)",
							hoverBorderColor: "rgba(41, 128, 185,1.0)",
          		data: data
						}
					]
				}
      },

      attachShowPostModal(barInteractionsMonthFirst, barChartInterMonth, topPostsMonth) {
      	barInteractionsMonthFirst.onclick = function(evt) {
	    		var activePoints = barChartInterMonth.getElementsAtEvent(evt);
	    		var idx = activePoints[0]['_index'];
	    		var post = topPostsMonth[idx];
	    		VueEvent.fire('setSelectedPost', post);
	    		$('#showPost').modal('show');
	    	};
      }
		}
	}
</script>