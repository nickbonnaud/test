<template>
</template>

<script>

	export default {
		props: ['profileSlug'],

		mounted() {
			Echo.private('transactions-change.' + this.profileSlug)
        .listen('TransactionsChange', (event) => {
          this.broadcastTransactionsChange(event);
        });
		},

		methods: {
      broadcastTransactionsChange: function(data) {
        if (data.transaction.status < 20) {
          VueEvent.fire('updateTransactionsPending');
        } else if(data.transaction.status == 20) {
          VueEvent.fire('updateTransactionsAll');
        }
      }
		}
	}
</script>