<template>
	<div class="chart tab-pane active" id="week-inter-chart">
		<canvas id="barInteractionsWeek" width="400" height="300"></canvas>
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
				topPostsInteractionsWeek: [],
			}
		},

		created() {
			this.fetchWeekInteractions();
		},

		methods: {
			fetchWeekInteractions() {
				axios.get('/api/web/posts/analytics/' + this.profileSlug + '?interactionsWeek=1&type=get')
          .then(this.setTopPostsWeek);
			},

			setTopPostsWeek({data}) {
				this.topPostsInteractionsWeek = data;
				this.init();
			},

			init() {
				var barInteractionsWeekFirst = $("#barInteractionsWeek").get(0);
				var barInteractionsWeek = barInteractionsWeekFirst.getContext("2d");
				var barInteractionsWeekData = this.formatBarData(this.topPostsInteractionsWeek);
	    	var barChartInterWeek = new Chart(barInteractionsWeek, {
	    		type: 'bar',
	    		data: barInteractionsWeekData,
	    		options: this.barChartOptionsInteractions
	    	});
	    	this.attachShowPostModal(barInteractionsWeekFirst, barChartInterWeek, this.topPostsInteractionsWeek);
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

      attachShowPostModal(barInteractionsWeekFirst, barChartInterWeek, topPostsWeek) {
      	barInteractionsWeekFirst.onclick = function(evt) {
	    		var activePoints = barChartInterWeek.getElementsAtEvent(evt);
	    		var idx = activePoints[0]['_index'];
	    		var post = topPostsWeek[idx];
	    		VueEvent.fire('setSelectedPost', post);
	    		$('#showPost').modal('show');
	    	};
      }
		}
	}
</script>