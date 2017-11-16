<template>
  <div class="modal fade" id="EmployeeChooseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header-timeline">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Please choose Team Member</h4>
        </div>
        <div class="modal-body-employee-picker">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-success">
              <div class="box-header with-border">
                <h3 class="box-title">On Shift</h3>
                <div class="box-tools pull-right"><span class="label label-success">{{ this.employeesOn.length }} on</span></div>
              </div>
              <div v-if="this.employeesOn.length > 0" class="box-body no-padding">
                <ul class="users-list clearfix">
                  <li v-for="employee in this.employeesOn" v-on:click="setEmployee(employee)">
                    <img v-if="employee.photo_path" :src="employee.photo_path" style="max-height: 75px;" alt="Employee Image">
                    <img v-else src="/images/icon-profile-photo.png" style="max-height: 75px;" alt="User Image">
                    <a class="users-list-name" href="#" v-on:click="setEmployee(employee)">{{ employee.first_name }} {{ employee.last_name }}</a>
                  </li>
                </ul>
              </div>
              <div v-else class="box-body">
                <h4>You are currently using Tip Tracking. At least one Team Member must be clocked-in.</h4>
                <h4>Please clock-in in the Team tab.</h4>
                <a :href="'/team/' + this.profileSlug">
                  <button class="btn btn-primary pull-right">Go to Team</button>
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
    props: ['profileSlug'],
		data() {
			return {
				employeesOn: [],
			}
		},

		mounted() {
			VueEvent.listen('showEmployeeSelectModal', this.setEmployeesOn.bind(this));
		},

		methods: {
			setEmployeesOn(employees) {
				this.employeesOn = employees;
				$('#EmployeeChooseModal').modal('show');
			},

      setEmployee(employee) {
        VueEvent.fire('employeeSelected', employee);
        $('#EmployeeChooseModal').modal('hide');
      }
		}
	}
</script>