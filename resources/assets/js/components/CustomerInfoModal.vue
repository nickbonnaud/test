<template>
	<div class="modal fade" id="CustomerinfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header-timeline">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title" id="CustomerinfoModal">Info | {{customer.first_name}} {{customer.last_name}}</h3>
        </div>
        <div class="modal-body-customer-info">
          <section class="content">
            <div class="row">
              <div class="col-md-6 col-sm-6 col-xs-12">
                <last-transaction :transaction="this.customer.last_transaction"></last-transaction>
              </div>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <last-interaction :post-interactions="this.customer.last_post_interactions"></last-interaction>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
	import LastTransaction from '../components/LastTransaction.vue';
	import lastInteraction from '../components/lastInteraction.vue';

	export default {
		components: {LastTransaction, lastInteraction},

		data() {
			return {
				customer: {},
			}
		},

		mounted() {
			VueEvent.listen('showCustomerInfoModal', this.setCustomerInfo.bind(this));
		},

		methods: {
			setCustomerInfo(user) {
				this.customer = user;
				$('#CustomerinfoModal').modal('show');
			}
		}
	}

</script>