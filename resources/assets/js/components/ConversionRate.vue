<template>
	<div>
		<div class="small-box bg-aqua">
			<div class="inner">
				<h3>{{ conversionRate }}%</h3>
				<p>Conversion Rate</p>
			</div>
			<div class="icon"><i class="fa fa-shopping-cart"></i></div>
			<a href="#" class="small-box-footer" data-toggle="modal" data-target="#ConversionRateModal">More info <i class="fa fa-arrow-circle-right"></i></a>
		</div>
		<analytics-info-modal id="ConversionRateModal" class-type="modal-header-analytics conversion_rate">
			<template slot="title">Conversion Rate</template>
			<template slot="body">
				<div class="sub-header">
	    		<h3>Your current Conversion Rate is <strong>{{ conversionRate }}%</strong></h3>
	    	</div>
	    	<hr>
	    	<p>Conversion Rate shows how effective your Posts are in <strong>creating in-store customers.</strong></p>
	    	<p>Calculated by the number of users who made a purchases within <strong>2 days</strong> of viewing a Post on your Pockeyt Feed.</p>
			</template>
		</analytics-info-modal>
	</div>
</template>

<script>
	import AnalyticsInfoModal from './AnalyticsInfoModal.vue';

	export default {
		props: ['profileSlug', 'totalViews'],

		components: {AnalyticsInfoModal},

		data() {
			return {
				totalPurchasesAll: 0,
			}
		},
		
		created() {
			this.fetchTotalPurchases();
		},

		computed: {
			conversionRate() {
				if (this.totalViews == 0) { return 0; }
				return ((this.totalPurchasesAll / this.totalViews) * 100).toFixed(2);
			}
		},

		methods: {
      fetchTotalPurchases() {
				axios.get('/api/web/analytics/posts/' + this.profileSlug + '?totalPurchases=1&type=count')
          .then(this.setTotalPurchases);
			},

			setTotalPurchases({data}) {
				this.totalPurchasesAll = data;
			}
		}
	}
</script>