<template>
</template>

<script>

	export default {
		props: ['profileSlug'],

		mounted() {
			Echo.private('transaction-success.' + this.profileSlug)
        .listen('TransactionSuccess', (event) => {
          this.notifySuccess(event);
        });
		},

		methods: {
      notifySuccess: function(data) {
        toastr["success"]("Payment Received!<br /><br /><button type='button' class='btn btn-default'>Ok</button>", data.user.first_name + " " + data.user.last_name + " has paid their bill.", {
          "newestOnTop": true,
          "timeOut": 0,
          "extendedTimeOut": 0,
        })
      }
		}
	}
</script>