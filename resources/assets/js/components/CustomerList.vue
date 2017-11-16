<template>
	<div class="scroll-contents">
		<div v-for="user in customerFilter">
		  <div class="col-sm-4 col-md-3">
		    <div class="box box-primary">
		      <div class="box-header with-border text-center">
		        <a v-on:click="showCustomerInfoModal(user)" class="customer-name-title" href="#">
		          <h3 class="box-title">{{user.first_name}} {{user.last_name}}</h3>
		        </a>
		        <div class="box-body">
		          <a v-on:click="showCustomerInfoModal(user)" href="#">
		            <img v-if="user.photo_path" :src="user.photo_path" class="profile-user-img img-responsive img-circle" alt="User Image">
		            <img v-else src="/images/icon-profile-photo.png" class="profile-user-img img-responsive img-circle">
		          </a>
		        </div>
		        <div class="box-footer">
	            <a href="#" v-on:click="employeeSelect(user)" class="btn btn-primary btn-block">
	              <b>Bill</b>
	            </a>
		          <div v-if="user.deal_data">
		            <a href="#" v-on:click="showDealModal(user)" class="btn btn-success btn-block btn-redeem">
		              <b>Redeem Deal</b>
		            </a>
		          </div>
		        </div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
</template>
<script>
	export default {
		props: ['profile'],

		data() {
			return {
				customersInLocation: [],
				employeesOn: [],
				selectedCustomer: {},
				selectedEmployeeId: '',
				openBillId: '',
				query: ''
			}
		},

		created() {
			this.fetchCustomersInLocation();
			this.fetchEmployeesOn();
		},

		mounted() {
			Echo.private('geofence.' + this.profile.slug)
        .listen('CustomerBreakGeoFence', (event) => {
          this.checkType(event);
      });
			VueEvent.listen('customerQueryChange', this.setQuery.bind(this));
			VueEvent.listen('employeeSelected', this.setSelectedEmployee.bind(this));
			VueEvent.listen('DealRedeemedSuccess',  this.removeDealData.bind(this));
		},

		computed: {
      customerFilter: function() {
        return this.findBy(this.customersInLocation, this.query, 'first_name', 'last_name');
      }
    },

		methods: {
      checkType: function(event) {
        if (event.type == "enter") {
          this.addUser(event);
        } else {
          this.removeUser(event);
        }
      },

      fetchCustomersInLocation() {
				axios.get('/api/web/location/customers/' + this.profile.slug + '?type=get')
          .then(this.setCustomersInLocation);
			},

			setCustomersInLocation({data}) {
				data.data.forEach(function(customer) {
					this.addUser(customer);
				}.bind(this));
			},

			addUser(newCustomer) {
				var index = this.customersInLocation.findIndex(function(currentCustomer) {
					return currentCustomer.id == this.id;
				}, newCustomer);
				if (index == -1) {
					this.customersInLocation.push(newCustomer);
				}
			},

			removeUser(customerToRemove) {
				var index = this.customersInLocation.findIndex(function(currentCustomer) {
					return currentCustomer.id == this.id;
				}, customerToRemove);
				if (index != -1) {
					this.customersInLocation.splice(index, 1);
				}
			},

			fetchEmployeesOn() {
				if (this.profile.tip_tracking_enabled) {
					axios.get('/api/web/users/' + this.profile.slug + '/search?dashboard=1&onShift=1')
	          .then(this.setEmployeesOn);
	      }
      },

      setEmployeesOn({data}) {
      	this.employeesOn = data.users;
      	if (this.employeesOn.length == 1) {
      		this.selectedEmployeeId = this.employeesOn[0].id;
      	}
      },

			setQuery(userInput) {
				this.query = userInput;
			},

			findBy: function(list, value, column_first, column_last) {
        return list.filter(function(customer) {
          return (customer[column_first].toLowerCase().includes(value.toLowerCase()) || customer[column_last].toLowerCase().includes(value.toLowerCase()));
        });
      },

      showCustomerInfoModal(user) {
      	VueEvent.fire('showCustomerInfoModal', user);
      },

      employeeSelect(user) {
      	this.selectedCustomer = user;
      	if (user.open_bill) {
      		this.openBillId = user.open_bill.id;
      		if (user.open_bill.employee_id) {
      			this.selectedEmployeeId = user.open_bill.employee_id;
      		}
      		this.goToBill();
      	} else {
      		if (this.profile.tip_tracking_enabled) {
	      		this.checkEmployeesOn();
	      	} else {
	      		this.goToBill();
	      	}
      	}
      },

      checkEmployeesOn() {
      	if (this.employeesOn.length == 1) {
      		this.goToBill();
      	} else {
      		this.showEmployeeSelectModal();
      	}
      },

      showEmployeeSelectModal() {
      	VueEvent.fire('showEmployeeSelectModal', this.employeesOn);
      },

      setSelectedEmployee(employee) {
      	this.selectedEmployeeId = employee.id;
      	this.goToBill();
      },

      goToBill() {
      	return window.location.href = "/bill/" + this.profile.slug + "/" + this.selectedCustomer.id + "?" + this.setEmployeeQuery() + this.setBillQuery();
      },

      setEmployeeQuery() {
      	if (this.selectedEmployeeId == '') {
      		return '';
      	} else {
      		return 'employee=' + this.selectedEmployeeId + '&';
      	}
      },

      setBillQuery() {
      	if (this.openBillId == '') {
      		return '';
      	} else {
      		return 'bill=' + this.openBillId;
      	}
      },

      showDealModal(user) {
      	VueEvent.fire('showDealModal', user);
      },

      removeDealData(customerId) {
      	this.customersInLocation.forEach(function(customer) {
      		if (customer.id == customerId) {
      			return customer.deal_data = null;
      		}
      	});
      }
		}
	}

</script>