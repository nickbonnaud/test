<template>
</template>

<script>

	export default {
		props: ['profileSlug'],

		mounted() {
			Echo.private('bill-request.' + this.profileSlug)
        .listen('CustomerRequestBill', (event) => {
          this.notifyBill(event);
        });
		},

		methods: {
      notifyBill: function(data) {
        toastr["info"](data.user.first_name + " " + data.user.last_name + " has requested their bill.<br /><br /><button type='button' class='btn btn-default'>Send Bill</button>", "Bill Requested!", {
          "newestOnTop": true,
          "timeOut": 0,
          "extendedTimeOut": 0,
          "onclick": function() {
            var employeeId = 'empty';
            route = "{{ route('bill.show', ['customerId' => 'id', 'employeeId' => 'eId']) }}"
            route = route.replace('id', data.user.id)
            location.href = route.replace('eId', employeeId);
          }
        })
      }
		}
	}

</script>