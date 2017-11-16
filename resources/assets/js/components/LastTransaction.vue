<template>
	<div>
		<div class="info-box">
      <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Date Last Purchase</span>
        <span v-if="transaction" class="info-box-number">{{ transaction.updated_at | setDateTime }}</span>
        <span v-else class="info-box-number">No Recent Purchases</span>
      </div>
    </div>
    <div v-if="transaction" class="box box-aqua collapsed-box">
      <div class="box-header with-border">
        <i class="fa fa-shopping-cart"></i>
        <h3 class="box-title">View Purchases</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
          </button>
        </div>
      </div>
      <div class="box-body">
        <div v-for="item in this.items">
          <p class="timeline-purchases-left">{{ item.quantity }} x {{ item.name }}</p>
          <p class="timeline-purchases-right">${{ (item.price / 100) | round }}</p>
        </div>
        <div class="box-footer timeline-list-footer">
          <div class="last-purchase-footer pull-right">Tax: ${{ (transaction.tax / 100) | round }}</div>
          <div class="last-purchase-footer pull-right" style="margin-bottom: 10px;">Tip: ${{ (transaction.tips / 100) | round }}</div>
          <div class="last-purchase-footer pull-right"><b>Total: ${{ (transaction.total / 100) | round }}</b></div>
        </div>
      </div>
    </div>
	</div>
</template>

<script>
	import moment from 'moment';

  export default {
		props: ['transaction'],

    data() {
      return {
        items: []
      }
    },

    watch: {
      transaction: function() {
        if (this.transaction) {
          this.items = JSON.parse(this.transaction.products);
        }
      }
    },

    filters: {
			setDateTime(value) {
        return moment(value).format("Do MMM YY [at] h:mm a");;
      },

			round(value) {
				return value.toFixed(2);
			}
		}
	}

</script>