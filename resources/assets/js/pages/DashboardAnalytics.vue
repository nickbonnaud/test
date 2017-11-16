<script>
	import ConversionRate from '../components/ConversionRate.vue';
	import RevenuePerPost from '../components/RevenuePerPost.vue';
	import topDay from '../components/topDay.vue';
	import TopHour from '../components/TopHour.vue';
	import ActivityDay from '../components/ActivityDay.vue';
	import ActivityHour from '../components/ActivityHour.vue';
	import TopPostsInteractions from '../components/TopPostsInteractions.vue';
	import TopPostsRevenue from '../components/TopPostsRevenue.vue';
	import InteractionBreakdown from '../components/InteractionBreakdown.vue';

	import moment from 'moment'; 
	

	export default {
		props: ['profileSlug'],
		components: {ConversionRate, RevenuePerPost, topDay, TopHour, ActivityDay, ActivityHour, TopPostsInteractions, TopPostsRevenue, InteractionBreakdown},

		data() {
			return {
				interactionsByDay: [],
				interactionsByHour: [],
				selectedPost: {}
			}
		},

		created() {
			this.fetchPostInteractionsByDay();
			this.fetchPostInteractionsByHour();
		},

		mounted() {
			VueEvent.listen('setSelectedPost', this.setSelectedPost.bind(this));
		},

		filters: {
			truncate(string, value) {
    		return string.substring(0, 80) + '...';
    	},
    	setDate(value) {
	    	return moment(value).format("Do MMM YY");
	    }
		},

		methods: {
			setSelectedPost(post) {
				this.selectedPost = post;
			},

			fetchPostInteractionsByDay() {
				axios.get('/api/web/analytics/posts/' + this.profileSlug + '?dayInteractions&type=get')
          .then(this.setPostInteractionsByDay);
			},

			setPostInteractionsByDay({data}) {
				for (var i = 0; i <= 6; i++) {

					if (data.find(d => d.date == i)) {
						this.interactionsByDay.push((data.find(d => d.date == i)).count);
					} else {
						this.interactionsByDay.push(0);
					}
      	}
			},

			fetchPostInteractionsByHour() {
				axios.get('/api/web/analytics/posts/' + this.profileSlug + '?hourInteractions&type=get')
          .then(this.setPostInteractionsByHour);
			},

			setPostInteractionsByHour({data}) {
				for (var i = 0; i <= 23; i++) {

					if (data.find(h => h.hour == i)) {
						this.interactionsByHour.push((data.find(h => h.hour == i)).count);
					} else {
						this.interactionsByHour.push(0);
					}
      	}
			}
		}
	}
	
</script>