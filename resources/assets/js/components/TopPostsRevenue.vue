<template>
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs pull-right">
			<li class="active"><a href="#week-revenue-chart" data-toggle="tab">7 Days</a></li>
			<li><a href="#month-revenue-chart" data-toggle="tab" v-on:click="getMonthRevenueData()">30 Days</a></li>
			<li><a href="#two-month-revenue-chart" data-toggle="tab" v-on:click="getTwoMonthRevenueData()">60 Days</a></li>
			<li class="pull-left header"><i class="fa fa-money"></i> Top Posts Revenue</li>
		</ul>
		<div class="tab-content">
			<week-revenue :profile-slug="this.profileSlug"></week-revenue>
			<month-revenue :profile-slug="this.profileSlug"></month-revenue>
			<two-month-revenue :profile-slug="this.profileSlug"></two-month-revenue>
		</div>
	</div>
</template>

<script>
	import WeekRevenue from '../components/WeekRevenue.vue';
	import MonthRevenue from '../components/MonthRevenue.vue';
	import TwoMonthRevenue from '../components/TwoMonthRevenue.vue';

	export default {
		props: ['profileSlug'],
		components: {WeekRevenue, MonthRevenue, TwoMonthRevenue},

		methods: {
			getMonthRevenueData() {
				VueEvent.fire('getMonthRevenueData');
			},

			getTwoMonthRevenueData() {
				VueEvent.fire('getTwoMonthRevenueData');
			}
		}
	}
</script>