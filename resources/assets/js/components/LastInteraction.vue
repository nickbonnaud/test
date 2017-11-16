<template>
	<div>
		<div class="info-box">
      <span class="info-box-icon bg-green"><i class="fa fa-eye"></i></span>

      <div v-if="postInteractions" class="info-box-content">
        <span class="info-box-text">Last post {{ this.interactionType }}</span>
        <span class="info-box-number">{{ this.interactionDate | setDateTime }}</span>
      </div>
      <div v-else class="info-box-content">
        <span class="info-box-text">Recent Activity</span>
        <span class="info-box-number">No Recent</span>
      </div>

    </div>
    <div v-if="postInteractions" class="box box-success collapsed-box">
      <div class="box-header with-border">
        <i class="fa fa-eye"></i>
        <h3 class="box-title">Post Details</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
          </button>
        </div>
      </div>
      <div class="box-body">
        <div class="analytics-modal-image">
          <img v-if="postInteractions.post.photo" :src="postInteractions.post.photo.url">
        </div>
        <div class="box-body-bottom">
          <h4 v-if="postInteractions.post.event_date" class="box-title customer-data-message">{{ postInteractions.post.title }}</h4>
          <h4 v-else class="box-title customer-data-message">{{ postInteractions.post.message }}</h4>
        </div>
        <hr style="margin-top: 10px; margin-bottom: 10px;">
        <p class="analytics-date-customer-data">Posted on <strong>{{ postInteractions.post.published_at | setDateTime }}</strong>.</p>
      </div>
    </div>
	</div>
</template>

<script>
	import moment from 'moment'; 

	export default {
		props: ['postInteractions'],

		data() {
			return {
				interactionType: '',
				interactionDate: ''
			}
		},

		watch: {
			postInteractions: function() {
				if (this.postInteractions) {
					this.setInteractionType();
				}
			}
		},

		filters: {
      setDateTime: function(value) {
        return moment(value).format("Do MMM YY [at] h:mm a");
      }
    },

		methods: {
			setInteractionType() {
				if (this.postInteractions.bookmarked) {
					console.log('1');
					if (this.postInteractions.shared) {
						console.log('2');
						if (new Date(this.postInteractions.bookmarked_on).getTime() > new Date(this.postInteractions.shared_on).getTime()) {
							console.log('3')
							this.interactionType = "bookmarked";
							this.interactionDate = this.postInteractions.bookmarked_on;
						} else {
							console.log('4')
							this.interactionType = "shared";
							this.interactionDate = this.postInteractions.shared_on;
						}
					} else {
						console.log('5')
						this.interactionType = "bookmarked";
						this.interactionDate = this.postInteractions.bookmarked_on;
					}
				} else if (this.postInteractions.shared) {
					console.log('6')
					this.interactionType = "shared";
					this.interactionDate = this.postInteractions.shared_on;
				} else {
					console.log('7')
					this.interactionType = "viewed";
					this.interactionDate = this.postInteractions.viewed_on;
				}
      }
		}
	}

</script>