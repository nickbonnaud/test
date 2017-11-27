<template>
  <div class="modal fade" id="redeemRewardModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header-timeline">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Loyalty Program Rewards | {{this.customer.first_name}} @{{this.customer.last_name}}</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <span class="pull-left">
              <h3 class="deal-item">{{this.customer.first_name}} has earned {{ this.loyaltyCard.unredeemed_rewards }}x {{this.loyaltyCard.reward}}</h3>
            </span>
            <span class="pull-right">
              <button v-on:click="redeemReward()" class="btn btn-block btn-success pull-right">Redeem One!</button>
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
				customer: {},
        loyaltyCard: {},
        requestSent: false
			}
		},

		mounted() {
			VueEvent.listen('showRewardModal', this.setCustomerReward.bind(this));
		},

		methods: {
			setCustomerReward(customer) {
				this.customer = customer;
        this.loyaltyCard = customer.loyalty_card;
				$('#redeemRewardModal').modal('show');
			},

      redeemReward() {
        if (this.requestSent) return;
        this.requestSent = true;
      	axios.patch('/api/web/loyalty-card/' + this.profileSlug + '/' + this.loyaltyCard.id, {
					'redeem_reward': true
				})
          .then(this.checkSuccess);
      },

      checkSuccess({data}) {
      	if (data.success) {
      		VueEvent.fire('toggleRedeemRequestSent', 'reward');
      	} else {
      		this.notifyFail();
      	}
      	$('#redeemRewardModal').modal('hide');
      },

      notifyFail() {
      	toastr["error"]("Unable to redeem Reward<br /><br /><button type='button' class='btn btn-default'>Ok</button>", "Unable to redeem reward for " + this.customer.first_name + " " + this.customer.last_name + ". Please contact Customer Support.", {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
        });
      }
		}
	}
</script>