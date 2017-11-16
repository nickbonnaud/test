<template>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="box box-success">
				<div class="box-header with-border">
					<h3 class="box-title">On Shift</h3>
					<div v-if="employeesOn.length != 0" class="box-tools pull-right"><span class="label label-success">{{ employeesOn.length }} on</span></div>
					<div v-else class="box-tools pull-right"><span class="label label-success">0 on</span></div>
				</div>
				<div class="box-body no-padding">
					<ul class="users-list clearfix">
						<li v-for="employee in employeesOn">
							<img v-if="employee.photo_path" :src="employee.photo_path" style="max-height: 60px;" alt="Employee Image">
							<img v-else src="/images/icon-profile-photo.png" style="max-height: 60px;" alt="User Image">
							<a class="users-list-name" href="#" v-on:click="toggleShift(employee)">{{ employee.first_name }} {{ employee.last_name }}</a>
							<button class="btn btn-danger shift-toggle" v-on:click="toggleShift(employee)">Clock Out</button>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="box box-warning">
				<div class="box-header with-border">
					<h3 class="box-title">Off Shift</h3>
					<div v-if="employeesOff.length != 0" class="box-tools pull-right"><span class="label label-warning">{{ employeesOff.length }} off</span></div>
					<div v-else class="box-tools pull-right"><span class="label label-warning">0 off</span></div>
				</div>
				<div class="box-body no-padding">
					<ul class="users-list clearfix">
						<li v-for="employee in employeesOff">
							<img v-if="employee.photo_path" :src="employee.photo_path" style="max-height: 60px;" alt="Employee Image">
							<img v-else src="/images/icon-profile-photo.png" style="max-height: 60px;" alt="User Image">
							<a class="users-list-name" href="#" v-on:click="toggleShift(employee)">{{ employee.first_name }} {{ employee.last_name }}</a>
							<button v-if="unlock != true" class="btn btn-success shift-toggle" v-on:click="toggleShift(employee)">Clock In</button>
							<button v-if="unlock == true" class="btn btn-danger shift-toggle" v-on:click="removeEmployee(employee)">Delete</button>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</template>

<script>

	export default {
		props: ['unlock', 'employeesOn', 'employeesOff', 'profileSlug'],

		methods: {

      toggleShift(employee) {
				axios.patch('/api/web/users/' + this.profileSlug + '/' + employee.id, {
					'on_shift': !employee.on_shift
				})
          .then(this.refresh);
			},

			removeEmployee(employee) {
				axios.patch('/api/web/users/' + this.profileSlug + '/' + employee.id, {
					'employer_id': null,
					'on_shift': false
				})
          .then(this.refresh);
			},

			refresh({data}) {
				if (data.employer_id == null) {
					VueEvent.fire('removeEmployee', data);
				} else {
					VueEvent.fire('shiftToggle', data);
				}
			},
		}
	}
</script>