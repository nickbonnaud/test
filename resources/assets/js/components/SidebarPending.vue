<template>
  <div class="tab-pane active" id="control-sidebar-home-tab">
    <h3 class="control-sidebar-heading" v-if="pendingTransactions.length != 0">Pending Transactions</h3>
    <h3 class="control-sidebar-heading" v-else>No Pending Transactions</h3>
    <ul class="control-sidebar-menu">
      <li v-for="transaction in pendingTransactions">
        <a href="javascript:void(0)">
          <i :class="statusClass(transaction.status)"></i>

          <div class="menu-info">
            <h4 class="control-sidebar-subheading">{{ transaction.customer_name }}</h4>

            <p>{{ statusText(transaction.status) }}</p>
          </div>
        </a>
      </li>
    </ul>
    <!-- /.control-sidebar-menu -->
  </div>
</template>

<script>
  export default {
    props: ['profileSlug'],

    data() {
      return {
        pendingTransactions: []
      }
    },

    created() {
      this.fetch();
      VueEvent.listen('updateTransactionsAll', this.fetch.bind(this));
      VueEvent.listen('updateTransactionsPending', this.fetch.bind(this));
    },

    methods: {
      fetch() {
        axios.get('/api/web/transactions/' + this.profileSlug + '?pending=1')
          .then(this.refresh);
      },

      refresh({data}) {
        this.pendingTransactions = data.data;
      },

      statusClass(status) {
        switch(status) {
          case 0:
          case 1:
          case 2:
          case 3:
          case 4:
            return "menu-icon fa fa-warning bg-red";
          case 11:
            return "menu-icon fa fa-thumbs-o-up bg-light-blue";
          case 12:
            return "menu-icon fa fa-bullhorn bg-yellow";
        }
      },

      statusText(status) {
        switch(status) {
          case 0:
            return "Failed to send Bill to customer";
          case 1:
            return "Unable to charge Card";
          case 2:
            return "Bill declined by customer";
          case 3:
            return "Not customer's bill";
          case 4:
            return "Error in customer's bill";
          case 11:
            return "Waiting customer approval";
          case 12:
            return "Requested bill";
        }
      },
    }
  }

</script>



