<template>
	<div class="chart tab-pane" id="two-month-inter-chart">
		<canvas id="barInteractionsTwoMonth" width="400" height="300"></canvas>
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
				topPostsInteractionsTwoMonth: [],
			}
		},

		mounted() {
			VueEvent.listen('getTwoMonthInterData', this.fetchTwoMonthInteractions.bind(this));
		},

		methods: {
			fetchTwoMonthInteractions() {
				if (this.topPostsInteractionsTwoMonth.length == 0) {
					axios.get('/api/web/posts/analytics/' + this.profileSlug + '?interactionsTwoMonth=1&type=get')
	          .then(this.setTopPostsTwoMonth);
	      }
			},

			setTopPostsTwoMonth({data}) {
				this.topPostsInteractionsTwoMonth = data;
				this.init();
			},

			init() {
				var barInteractionsTwoMonthFirst = $("#barInteractionsTwoMonth").get(0);
				var barInteractionsTwoMonth = barInteractionsTwoMonthFirst.getContext("2d");
				var barInteractionsTwoMonthData = this.formatBarData(this.topPostsInteractionsTwoMonth);
	    	var barChartInterTwoMonth = new Chart(barInteractionsTwoMonth, {
	    		type: 'bar',
	    		data: barInteractionsTwoMonthData,
	    		options: this.barChartOptionsInteractions
	    	});
	    	this.attachShowPostModal(barInteractionsTwoMonthFirst, barChartInterTwoMonth, this.topPostsInteractionsTwoMonth);
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

      attachShowPostModal(barInteractionsTwoMonthFirst, barChartInterTwoMonth, topPostsTwoMonth) {
      	barInteractionsTwoMonthFirst.onclick = function(evt) {
	    		$('#showPost').modal('show');
	    		var activePoints = barChartInterTwoMonth.getElementsAtEvent(evt);
	    		var idx = activePoints[0]['_index'];
	    		var post = topPostsTwoMonth[idx];
	    		VueEvent.fire('setSelectedPost', post);
	    	};
      }
		}
	}
</script>