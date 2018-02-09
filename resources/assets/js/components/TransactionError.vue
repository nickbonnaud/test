<template>
</template>

<script>

	export default {
		props: ['profileSlug'],

		mounted() {
			Echo.private('transaction-error.' + this.profileSlug)
        .listen('TransactionError', (event) => {
          console.log(event);
          this.notifyError(event);
        });
		},

		methods: {
      notifyError: function(data) {
        console.log(data);
        if (data.transaction.status === 1) {
          toastr["error"]("Charge Failed<br /><br /><button type='button' class='btn btn-default'>Ok</button>", "Unable to charge " + data.user.first_name + " " + data.user.last_name + ". Unable to process payment for transaction id: " + data.transaction.id + ". Please contact Customer Support.", {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
          });
        } else if (data.transaction.status === 2) {
          toastr["error"]("Bill Declined<br /><br /><button type='button' class='btn btn-default'>Ok</button>", data.user.first_name + " " + data.user.last_name + " declined the bill. Please check with " + data.user.first_name + " to settle dispute and re-submit the bill.", {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
          });
        } else if (data.transaction.status === 3) {
          toastr["error"]("Wrong Customer!<br /><br /><button type='button' class='btn btn-default'>Ok</button>", data.user.first_name + " " + data.user.last_name + " has claimed that you started or sent a bill to them in error. Please check with " + data.user.first_name + " to ensure you are sending the bill to the correct customer.", {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
          });
        } else if (data.transaction.status === 4) {
          toastr["error"]("Error in Bill!<br /><br /><button type='button' class='btn btn-default'>Ok</button>", data.user.first_name + " " + data.user.last_name + " has noticed an error in their bill. Please check with " + data.user.first_name + " to correct the bill.", {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
          });
        }
      }
		}
	}

</script>