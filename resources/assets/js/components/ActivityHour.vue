<template>
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs pull-right">
			<li class="active"><a href="#hour-inter-chart" data-toggle="tab">Interactions</a></li>
			<li><a href="#hour-revenue-chart" data-toggle="tab" v-on:click="getHourRevenueData()">Revenue</a></li>
			<li class="pull-left header"><i class="fa fa-hourglass-2"></i> Percentage Activity by Time</li>
		</ul>
		<div class="tab-content">
			<hour-interactions v-bind:total-views="totalViews" :interactions-hour="interactionsHour"></hour-interactions>
			<hour-revenue :fetch-data="dataFetched" :profile-slug="profileSlug" v-bind:total-revenue="totalRevenue"></hour-revenue>
		</div>
	</div>
</template>

<script>
	import HourInteractions from '../components/HourInteractions.vue';
	import HourRevenue from '../components/HourRevenue.vue';

	export default {
		props: ['profileSlug', 'totalViews', 'totalRevenue', 'interactionsHour'],
		components: {HourInteractions, HourRevenue},

		data() {
			return {
				dataFetched: false
			}
		},

		methods: {
			getHourRevenueData() {
				if (!this.dataFetched) {
					this.dataFetched = true;
				}
			}
		}
	}
</script>