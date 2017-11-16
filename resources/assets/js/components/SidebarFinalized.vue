<template>
	<div class="tab-pane" id="control-sidebar-settings-tab">
    <div v-if="finalizedTransactions.length != 0">
      <h3 class="control-sidebar-heading">Recent transactions</h3>
      <ul class="control-sidebar-menu">
        <li v-for="transaction in finalizedTransactions">
          <a href="javascript:void(0)" v-if="transaction.status === 20">
            <i class="menu-icon fa fa-star-o bg-green"></i>

            <div class="menu-info">
              <h4 class="control-sidebar-subheading">{{ transaction.customer_name }}</h4>

              <p>Paid!</p>
            </div>
          </a>
        </li>
      </ul>
    </div>
    <div v-else>
      <h3 class="control-sidebar-heading">No recent transactions</h3>
    </div>
  </div>
</template>

<script>
  export default {
    props: ['profileSlug'],

    data() {
      return {
        finalizedTransactions: []
      }
    },

    created() {
      this.fetch();
      VueEvent.listen('updateTransactionsAll', this.fetch.bind(this));
    },

    methods: {
      fetch() {
        axios.get('/api/web/transactions/' + this.profileSlug + '?finalized=1')
          .then(this.refresh);
      },

      refresh({data}) {
      	this.finalizedTransactions = data.data;
      }
    }
  }

</script>