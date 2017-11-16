<template>
	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
		<div class="small-box bg-green">
			<div class="inner">
				<h3>${{ netSales }}</h3>
				<p>Net Sales</p>
			</div>
			<div class="icon"><i class="fa fa-usd"></i></div>
			<a href="#" class="small-box-footer" v-on:click="modalNetSales()">More info <i class="fa fa-arrow-circle-right"></i></a>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['transactions'],

		computed: {
      netSales() {
        var transactions = this.transactions;
				if (transactions.length == 0) { return 0}
				var total = 0;
				transactions.forEach(function(transaction) {
					total = total + transaction.net_sales;
				});
				return (total / 100).toFixed(2);
      }
		},

		methods: {
			modalNetSales() {
				var data = {
					modalType: "Net Sales",
					modalData: this.netSales
				};
				VueEvent.fire('showModalSalesHistory', data);
			}
		}
	}
</script>