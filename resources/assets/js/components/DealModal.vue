<template>
  <div class="modal fade" id="redeemDealModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header-timeline">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Purchased Deals | {{this.customerDeal.first_name}} @{{this.customerDeal.last_name}}</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <span v-if="this.customerDeal.deal_data" class="pull-left">
              <h3 class="deal-item">{{ this.products }}</h3>
            </span>
            <span class="pull-right">
              <button v-on:click="redeemDeal()" class="btn btn-block btn-success pull-right">Redeem!</button>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
	export default {
    props: ['profileSlug'],
		data() {
			return {
				customerDeal: {},
        products: "",
        requestSent: false
			}
		},

		mounted() {
			VueEvent.listen('showDealModal', this.setCustomerDeal.bind(this));
		},

		methods: {
			setCustomerDeal(customer) {
				this.customerDeal = customer;
        this.products = customer.deal_data.products;
				$('#redeemDealModal').modal('show');
			},

      redeemDeal() {
        if (this.requestSent) return;
        this.requestSent = true;
      	axios.patch('/api/web/deals/' + this.profileSlug + '/' + this.customerDeal.deal_data.id, {
					'redeem_deal': true
				})
          .then(this.checkSuccess);
      },

      checkSuccess({data}) {
      	if (data.success) {
      	 VueEvent.fire('toggleRedeemRequestSent', 'deal');
      	} else {
      		this.notifyFail();
      	}
      	$('#redeemDealModal').modal('hide');
      },

      notifyFail() {
      	toastr["error"]("Unable to redeem Deal<br /><br /><button type='button' class='btn btn-default'>Ok</button>", "Unable to redeem deal for " + this.customerDeal.first_name + " " + this.customerDeal.last_name + ". Please contact Customer Support.", {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
        });
      }
		}
	}
</script>