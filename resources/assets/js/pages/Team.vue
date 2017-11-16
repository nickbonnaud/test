<script>

	import ShiftTracker from '../components/ShiftTracker.vue';
	import SearchUsers from '../components/SearchUsers.vue';

	export default {
		props: ['teamMembers', 'profileSlug'],
		components: {ShiftTracker, SearchUsers},

		data() {
			return {
				employeesOn: [],
				employeesOff: [],
				unlock: false,
			}
		},

		created() {
			this.employeesOn = this.employeeOnFilter();
			this.employeesOff = this.employeeOffFilter();
		},

		mounted() {
			VueEvent.listen('shiftToggle', this.updateEmployeeShift.bind(this));
			VueEvent.listen('removeEmployee', this.removeEmployee.bind(this));
			VueEvent.listen('addEmployee', this.addEmployee.bind(this));
		},

		methods: {

			updateEmployeeShift(employee) {
				if (employee.on_shift) {
					this.getEmployeeIndex(employee, this.employeesOff);
					this.employeesOn.push(employee);
				} else {
					this.getEmployeeIndex(employee, this.employeesOn);
					this.employeesOff.push(employee);
				}
			},

			removeEmployee(employee) {
				this.getEmployeeIndex(employee, this.employeesOff);
			},

			addEmployee(employee) {
				this.employeesOff.push(employee);
			},

			getEmployeeIndex(employee, employeeArray) {
				for (var i = employeeArray.length -1; 1 >= 0; i--) {
					if (employeeArray[i].id == employee.id) {
						employeeArray.splice(i, 1);
						break;
					}
				}
			},

			employeeOnFilter() {
				return this.teamMembers.filter(function(employee) {
					return employee.on_shift == true;
				})
			},

			employeeOffFilter() {
				return this.teamMembers.filter(function(employee) {
					return employee.on_shift == false;
				})
			}
		}
	}
	
</script>