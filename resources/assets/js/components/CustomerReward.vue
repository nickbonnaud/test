<template>
</template>

<script>

	export default {
		props: ['profileSlug'],

		mounted() {
			Echo.private('reward.' + this.profileSlug)
        .listen('CustomerEarnReward', (event) => {
          console.log("hello");
          this.notifyReward(event);
        });
		},

		methods: {

      notifyReward: function(data) {
        toastr["info"]("Loyalty reward earned!<br /><br /><button type='button' class='btn btn-default'>Ok</button>", data.user.first_name + " " + data.user.last_name + " has earned: " + data.loyaltyProgram.reward, {
          "newestOnTop": true,
          "timeOut": 0,
          "extendedTimeOut": 0,
        })
      }
		}
	}

</script>