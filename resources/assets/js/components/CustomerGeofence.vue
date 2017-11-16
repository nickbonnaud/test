<template>
</template>

<script>

	export default {
		props: ['profileSlug'],

		mounted() {
			Echo.private('geofence.' + this.profileSlug)
        .listen('CustomerBreakGeoFence', (event) => {
          this.checkType(event);
        });


      pusher.subscribe("transaction" + this.profileSlug)
        .bind('App\\Events\\TransactionsChange', this.loadTransactions);
		},

		methods: {

			checkType: function(event) {
        if (event.type == "enter") {
          this.addUser(event);
        } else {
          this.removeUser(event);
        }
      },

      addUser: function(data) {
        if (data.user) {
          var activeCustomer = data.user;
        } else {
          var activeCustomer = data;
        }
        var users = this.users;
        console.log(users);
        var purchases = this.purchases;
        if(users.length == 0) {
          activeCustomer['lastActive'] = Date.now();
          users.push(activeCustomer);
        } else {
          var found = false;
          for (i=users.length - 1; i >= 0; i --) {
            if(users[i].id == activeCustomer.id) {
              users[i].lastActive = Date.now();
              found = true;
            }
          }
          if(!found) {
            activeCustomer['lastActive'] = Date.now();
            users.push(activeCustomer);
          }
        }
        console.log(users);
        customer.getRedeemableDeals(activeCustomer.id);
      },

      removeUser: function(data) {
        var leavingCustomer = data.user;
        var users = this.users;
        
        if(users.length > 0) {
          for (i=users.length - 1; i >= 0; i --) {
            if (users[i].id == leavingCustomer.id) {
              users.splice(i, 1);
            }
          }
        }
      }
		}
	}

</script>