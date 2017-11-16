<template>
	<div class="box-body">
		<canvas id="donutInteractions" width="200" height="200"></canvas>
	</div>
</template>

<script>
	export default {
		props: ['selectedPost'],

		watch: {
			selectedPost: function() {
				this.init(this.selectedPost);
			}
		},

		methods: {

			init(selectedPost) {
				$('#showPost').on('shown.bs.modal', function (event) {
					var post = selectedPost;
					var donutInteractionsCanvas = $("#donutInteractions").get(0).getContext("2d");
		    	var donutChartInter = new Chart(donutInteractionsCanvas, {
		    			type: 'pie',
		    			data: {
		    				labels: ['Views', 'Shares', 'Bookmarks'],
		    				datasets: [{
		    					backgroundColor: [
		  							'rgba(52, 152, 219, .8)',
		  							'rgba(155, 89, 182, .8)',
		  							'rgba(46, 204, 113, .8)'
		  						],
		  						hoverBackgroundColor: [
		  							'rgba(41, 128, 185, 1.0)',
		  							'rgba(142, 68, 173, 1.0)',
		  							'rgba(39, 174, 96, 1.0)'
		  						],
		  						data: [post.views, post.shares, post.bookmarks],
		  						options: {
		  							responsive: true
		  						}
		  					}]
		    			}
		    		});
				});
			}
		}
	}
</script>