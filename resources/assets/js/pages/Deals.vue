<script>
	import InputMoney from '../components/InputMoney.vue';

	export default {
		components: {InputMoney},

		data() {
			return {
				purchasedDeals: []
			}
		},
		computed: {
			redeemed: function() {
				var count = 0;
				this.purchasedDeals.forEach(function(e) {
					if (e.redeemed == true) {
						count++
					}
				});
				return count;
			},
			outstanding: function() {
				var count = 0;
				this.purchasedDeals.forEach(function(e) {
					if (e.redeemed == false) {
						count++
					}
				});
				return count;
			},
			total: function() {
				var earned = 0;
				this.purchasedDeals.forEach(function(e) {
						earned = earned + e.total;
				});
				return (earned / 100);
			}
		},

		methods: {
			getPurchasedDeals(postId) {
				axios.get('/api/web/deals/' + postId)
					.then(this.refresh);
			},

			refresh({data}) {
				this.purchasedDeals = data;
			}
		}
	}
	
</script>