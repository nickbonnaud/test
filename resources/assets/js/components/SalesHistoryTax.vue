<template>
	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
		<div class="small-box bg-red">
			<div class="inner">
				<h3>${{ netTaxes }}</h3>
				<p>Net Sales Tax</p>
			</div>
			<div class="icon"><i class="fa fa-balance-scale"></i></div>
			<a href="#" class="small-box-footer" v-on:click="modalSalesTax()">More info <i class="fa fa-arrow-circle-right"></i></a>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['transactions'],

		computed: {
      netTaxes() {
        var transactions = this.transactions;
				if (transactions.length == 0) { return 0}
				var total = 0;
				transactions.forEach(function(transaction) {
					total = total + transaction.tax;
				});
				return (total / 100).toFixed(2);
      }
		},

		methods: {
			modalSalesTax() {
				var data = {
					modalType: "Net Sales Taxes",
					modalData: this.netTaxes
				};
				VueEvent.fire('showModalSalesHistory', data);
			}
		}
	}
</script>