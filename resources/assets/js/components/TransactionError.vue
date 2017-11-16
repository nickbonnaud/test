<template>
</template>

<script>

	export default {
		props: ['profileSlug'],

		mounted() {
			Echo.private('transaction-error.' + this.profileSlug)
        .listen('TransactionError', (event) => {
          this.notifyError(event);
        });
		},

		methods: {
      notifyError: function(data) {
        if (data.transaction.status === 1) {
          toastr["error"]("Charge Failed<br /><br /><button type='button' class='btn btn-default'>Ok</button>", "Unable to charge " + data.user.first_name + " " + data.user.last_name + ". Unable to process payment for transaction id: " + data.transaction.id + ". Please contact Customer Support.", {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
          })
        } else if (data.transaction.status === 2) {
          toastr["error"]("Bill Declined<br /><br /><button type='button' class='btn btn-default'>Ok</button>", data.user.first_name + " " + data.user.last_name + " declined the bill. Please check with " + data.user.first_name + " to settle dispute and re-submit the bill.", {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
          })
        }
      }
		}
	}

</script>