<template>
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs pull-right">
			<li class="active"><a href="#week-inter-chart" data-toggle="tab">7 Days</a></li>
			<li><a href="#month-inter-chart" data-toggle="tab" v-on:click="getMonthInteractionData()">30 Days</a></li>
			<li><a href="#two-month-inter-chart" data-toggle="tab" v-on:click="getTwoMonthInteractionData()">60 Days</a></li>
			<li class="pull-left header"><i class="fa fa-hand-o-up"></i> Top Posts Interactions</li>
		</ul>
		<div class="tab-content">
			<week-interaction :profile-slug="this.profileSlug"></week-interaction>
			<month-interaction :profile-slug="this.profileSlug"></month-interaction>
			<two-month-interaction :profile-slug="this.profileSlug"></two-month-interaction>
		</div>
	</div>
</template>

<script>
	import WeekInteraction from '../components/WeekInteraction.vue';
	import MonthInteraction from '../components/MonthInteraction.vue';
	import TwoMonthInteraction from '../components/TwoMonthInteraction.vue';

	export default {
		props: ['profileSlug'],
		components: {WeekInteraction, MonthInteraction, TwoMonthInteraction},

		methods: {
			getMonthInteractionData() {
				VueEvent.fire('getMonthInterData');
			},

			getTwoMonthInteractionData() {
				VueEvent.fire('getTwoMonthInterData');
			}
		}
	}
</script>