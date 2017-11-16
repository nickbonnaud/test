<template>
	<div>
		<div class="small-box bg-yellow">
			<div class="inner">
				<h3 class="analytics-bubble">{{ topDay }}</h3>
				<p>Best Day to Post</p>
			</div>
			<div class="icon"><i class="fa fa-calendar-plus-o"></i></div>
			<a href="#" class="small-box-footer" data-toggle="modal" data-target="#BestDayModal">More info <i class="fa fa-arrow-circle-right"></i></a>
		</div>
		<analytics-info-modal id="BestDayModal" class-type="modal-header-analytics best_day">
			<template slot="title">Best Day to Post</template>
			<template slot="body">
				<div class="sub-header">
	    		<h3 v-show="topDay != 'No Data'">The best day to publish a Post is <strong>{{ topDay }}</strong>.</h3>
	    		<h3 v-show="topDay == 'No Data'">More Data Required.</h3>
	    	</div>
	    	<hr>
	    	<p v-show="topDay != 'No Data'">{{ topDay }} is the <strong>day of the week</strong> your Post is most likely to be Viewed, Shared, or Bookmarked.</p>
	    	<p v-show="topDay == 'No Data'">This is the <strong>day of the week</strong> your Post is most likely to be Viewed, Shared, or Bookmarked.</p>
	    	<p>Calculated by the day with highest percentage of Views, Shares, and Bookmarks</p>
			</template>
		</analytics-info-modal>
	</div>
</template>

<script>
	import AnalyticsInfoModal from './AnalyticsInfoModal.vue';

	export default {
		props: ['interactionsDay'],

		components: {AnalyticsInfoModal},
		
		computed: {
			topDay() {
				if (this.interactionsDay.length == 0) { return "Loading...";}
				if (this.interactionsDay.filter((day) => (day == 0)).length == 7) { return "No Data";}
				var dayKey = this.interactionsDay.indexOf(Math.max.apply(Math, this.interactionsDay));
				switch(dayKey) {
					case 0:
		        return "Monday";
		      case 1:
		      	return "Tuesday";
		      case 2:
		        return "Wednesday";
		      case 3:
		        return "Thursday";
		      case 4:
		        return "Friday";
		      case 5:
		        return "Saturday";
		      case 6:
		        return "Sunday";
				}
			}
		}
	}
</script>