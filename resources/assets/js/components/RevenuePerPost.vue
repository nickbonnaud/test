<template>
	<div>
		<div class="small-box bg-green">
			<div class="inner">
				<h3>${{ revenuePerPost }}</h3>
				<p>Revenue Per Post</p>
			</div>
			<div class="icon"><i class="fa fa-usd"></i></div>
			<a href="#" class="small-box-footer" data-toggle="modal" data-target="#RevenuePerPostModal">More info <i class="fa fa-arrow-circle-right"></i></a>
		</div>
		<analytics-info-modal id="RevenuePerPostModal" class-type="modal-header-analytics revenue_per_post">
			<template slot="title">Revenue Per Post</template>
			<template slot="body">
				<div class="sub-header">
	    		<h3>Your current Revenue Per Post is <strong>${{ revenuePerPost }}</strong></h3>
	    	</div>
	    	<hr>
	    	<p>Revenue per Post shows how effective your Pockeyt Posts are in <strong>generating revenue.</strong></p>
	    	<p>Calculated by your total revenue earned on Pockeyt divided by the number of uniquely viewed posts.</p>
			</template>
		</analytics-info-modal>
	</div>
</template>

<script>
	import AnalyticsInfoModal from './AnalyticsInfoModal.vue';

	export default {
		props: ['totalViews', 'totalRevenue'],

		components: {AnalyticsInfoModal},
		
		computed: {
			revenuePerPost() {
				if (this.totalViews == 0) { return 0; }
				return ((this.totalRevenue / this.totalViews) / 100).toFixed(2);
			}
		}
	}
</script>