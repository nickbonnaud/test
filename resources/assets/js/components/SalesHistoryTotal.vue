<template>
	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
		<div class="small-box bg-green">
			<div class="inner">
				<h3 class="analytics-bubble">${{ netTotal }}</h3>
				<p>Net Total</p>
			</div>
			<div class="icon"><i class="fa fa-money"></i></div>
			<a href="#" class="small-box-footer" v-on:click="modalTotal()">More info <i class="fa fa-arrow-circle-right"></i></a>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['transactions'],

		computed: {
      netTotal() {
        var transactions = this.transactions;
				if (transactions.length == 0) { return 0}
				var total = 0;
				transactions.forEach(function(transaction) {
					total = total + transaction.total;
				});
				return (total / 100).toFixed(2);
      }
		},

		methods: {
			modalTotal() {
				var data = {
					modalType: "Net Total",
					modalData: this.netTotal
				};
				VueEvent.fire('showModalSalesHistory', data);
			}
		}
	}
</script>