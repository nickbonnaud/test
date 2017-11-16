<template>
	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
		<div class="small-box bg-aqua">
			<div class="inner">
				<h3 class="analytics-bubble">${{ netTips }}</h3>
				<p>Net Tips</p>
			</div>
			<div class="icon"><i class="fa fa-thumbs-o-up"></i></div>
			<a href="#" class="small-box-footer" v-on:click="modalTips()">More info <i class="fa fa-arrow-circle-right"></i></a>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['transactions'],

		computed: {
      netTips() {
        var transactions = this.transactions;
				if (transactions.length == 0) { return 0}
				var total = 0;
				transactions.forEach(function(transaction) {
					total = total + transaction.tips;
				});
				return (total / 100).toFixed(2);
      }
		},

		methods: {
			modalTips() {
				var data = {
					modalType: "Net Tips",
					modalData: this.netTips
				};
				VueEvent.fire('showModalSalesHistory', data);
			}
		}
	}
</script>