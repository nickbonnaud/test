<template>
	<div>
		<div class="small-box bg-red">
			<div class="inner">
				<h3 class="analytics-bubble">{{ topHour }}</h3>
				<p>Best Time to Post</p>
			</div>
			<div class="icon"><i class="fa  fa-clock-o"></i></div>
			<a href="#" class="small-box-footer" data-toggle="modal" data-target="#BestHourModal">More info <i class="fa fa-arrow-circle-right"></i></a>
		</div>
		<analytics-info-modal id="BestHourModal" class-type="modal-header-analytics best_hour">
			<template slot="title">Best Time to Post</template>
			<template slot="body">
				<div class="sub-header">
	    		<h3 v-show="topHour != 'No Data'">The best time to publish a Post is <strong>{{ topHour }}</strong>.</h3>
	    		<h3 v-show="topHour == 'No Data'">More Data Required.</h3>
	    	</div>
	    	<hr>
	    	<p v-show="topHour != 'No Data'">{{ topHour }} is the <strong>time of day</strong> your Post is most likely to be Viewed, Shared, or Bookmarked.</p>
	    	<p v-show="topHour == 'No Data'">This is the <strong>time of day</strong> your Post is most likely to be Viewed, Shared, or Bookmarked.</p>
	    	<p>Calculated by the hour with highest percentage of Views, Shares, and Bookmarks</p>
			</template>
		</analytics-info-modal>
	</div>
</template>

<script>
	import AnalyticsInfoModal from './AnalyticsInfoModal.vue';

	export default {
		props: ['interactionsHour'],

		components: {AnalyticsInfoModal},
		
		computed: {
			topHour() {
				if (this.interactionsHour.length == 0) { return "Loading...";}
				if (this.interactionsHour.filter((hour) => (hour == 0)).length == 24) { return "No Data";}
				var hour = this.interactionsHour.indexOf(Math.max.apply(null, this.interactionsHour));
				var hourAdjusted = hour - 12;
		    if (hourAdjusted == -12) {
		      var topHour = "12am - 1am";
		    } else if (hourAdjusted < 0) {
		      var endTime = hour + 1;
		      var topHour = hour + 'am - ' + endTime + 'am';
		    } else if (hourAdjusted == 0) {
		      var topHour = "12pm - 1pm";
		    } else {
		      var endTime = hourAdjusted + 1;
		      var topHour = hourAdjusted + 'pm - ' + endTime + 'pm';
		    }
		    return topHour;
			}
		}
	}
</script>