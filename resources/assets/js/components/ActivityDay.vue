<template>
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs pull-right">
			<li class="active"><a href="#day-inter-chart" data-toggle="tab">Interactions</a></li>
			<li><a href="#day-revenue-chart" data-toggle="tab" v-on:click="getDayRevenueData()">Revenue</a></li>
			<li class="pull-left header"><i class="fa fa-calendar-o"></i> Percentage Activity by Day</li>
		</ul>
		<div class="tab-content">
			<day-interactions v-bind:total-views="totalViews" :interactions-day="interactionsDay"></day-interactions>
			<day-revenue :fetch-data="dataFetched" :profile-slug="profileSlug" v-bind:total-revenue="totalRevenue"></day-revenue>
		</div>
	</div>
</template>

<script>
	import DayInteractions from '../components/DayInteractions.vue';
	import DayRevenue from '../components/DayRevenue.vue';

	export default {
		props: ['profileSlug', 'totalViews', 'totalRevenue', 'interactionsDay'],
		components: {DayInteractions, DayRevenue},

		data() {
			return {
				dataFetched: false
			}
		},

		methods: {
			getDayRevenueData() {
				if (!this.dataFetched) {
					this.dataFetched = true;
				}
			}
		}
	}
</script>