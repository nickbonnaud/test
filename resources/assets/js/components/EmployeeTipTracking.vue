<template>
	<div class="row">
		<div v-show="this.selectedEmployees.length > 0">
			<h2 class="page-header" style="padding-left: 15px;">Employee Sales Breakdown</h2>
			<div v-for="employee in this.selectedEmployees" class="col-md-4">
				<div class="box box-widget widget-user">
					<div class="widget-user-header bg-aqua-active">
						<h3 class="widget-user-username">{{ employee.first_name }} {{ employee.last_name }}</h3>
						<h5 class="widget-user-desc">{{ employee.role }}</h5>
					</div>
					<div class="widget-user-image">
						<img v-if="employee.photo_path" :src="employee.photo_path" class="img-circle" alt="User Image">
						<img v-else class="img-circle" src="/images/icon-profile-photo.png">
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-sm-4 border-right">
								<div class="description-block">
									<h5 class="description-header">${{ employeeSales(employee.id) }}</h5>
									<span class="description-text">SALES</span>
								</div>
							</div>
							<div class="col-sm-4 border-right">
								<div class="description-block">
									<h5 class="description-header">${{ employeeTips(employee.id) }}</h5>
									<span class="description-text">TIPS</span>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="description-block">
									<h5 class="description-header">${{ employeeTotal(employee.id) }}</h5>
									<span class="description-text">TOTAL</span>
								</div>
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
		props: ['selectedEmployees', 'transactions'],

		methods: {
      employeeSales(employeeId) {
				var transactions = this.transactions;
				if (transactions.length == 0) { return 0}
				var total = 0;
				transactions.forEach(function(transaction) {
					if (transaction.employee_id == employeeId) {
						total = total + transaction.net_sales + transaction.tax;
					}
				});
				return (total / 100).toFixed(2);
			},

			employeeTips(employeeId) {
				var transactions = this.transactions;
				if (transactions.length == 0) { return 0}
				var totalTips = 0;
				transactions.forEach(function(transaction) {
					if (transaction.employee_id == employeeId) {
						totalTips = totalTips + transaction.tips;
					}
				});
				return (totalTips / 100).toFixed(2);
			},

			employeeTotal(employeeId) {
				var transactions = this.transactions;
				if (transactions.length == 0) { return 0}
				var total = 0;
				transactions.forEach(function(transaction) {
					if (transaction.employee_id == employeeId) {
						total = total + transaction.total;
					}
				});
				return (total / 100).toFixed(2);
			},
		}
	}
</script>