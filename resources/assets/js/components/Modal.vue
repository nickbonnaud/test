<template>
	<div class="modal fade" :id="id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header-analytics best_hour">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h3 class="modal-title" ><slot name="title"></slot></h3>
	      </div>
	      <div class="modal-body-analytics-bubble">
	      	<slot name="body"></slot>
	      </div>
	    </div>
	  </div>
	</div>
</template>

<script>

	export default {
		props: ['id'],

		mounted() {
			VueEvent.listen('showModalSalesHistory', this.showModal.bind(this));
		},

		methods: {
      showModal: function(data) {
    		$('#' + this.id).modal('show');
      }
		}
	}
</script>