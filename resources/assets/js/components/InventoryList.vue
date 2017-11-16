<template>
	<div>
		<div v-if="inventory.length > 0" >
			<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" v-for="product in productFilter">
		    <div v-if="product.photo" class="box-inventory" v-on:click="addProduct(product)">
		      <div class="box-body-inventory-image">
		        <img :src="product.thumbnail">
		      </div>
		      <div class="box-footer-inventory">
		        <b>{{ product.name | truncate }}</b>
		      </div>
		    </div>
		    <div v-else class="box-inventory" v-on:click="addProduct(product)">
		      <div class="box-body-inventory">
		        <p class="inventory-text"><strong>{{ product.name | truncateLong }}</strong></p>
		      </div>
		      <div class="box-footer-inventory">
		        <b>Add</b>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['profileSlug'],

		data() {
			return {
				inventory: [],
				query: ''
			}
		},

		created() {
			this.fetchInventory();
		},

		mounted() {
			VueEvent.listen('productQueryChange', this.setQuery.bind(this));
		},

		computed: {
      productFilter: function() {
        return this.findBy(this.inventory, this.query, 'name');
      }
    },

		filters: {
      truncate: function(string, value) {
        if (string.length > 20) {
          return string.substring(0, 20) + '...';
        } else {
          return string;
        }
      },

      truncateLong: function(string, value) {
        if (string.length > 50) {
          return string.substring(0, 50) + '...';
        } else {
          return string;
        }
      }
    },

		methods: {
			setQuery(userInput) {
				this.query = userInput;
			},

			findBy: function(list, value, column) {
        return list.filter(function(product) {
          return product[column].toLowerCase().includes(value.toLowerCase());
        });
      },

			fetchInventory() {
				axios.get('/api/web/products/' + this.profileSlug)
          .then(this.setInventory);
			},

			setInventory({data}) {
				this.inventory = data.data;
			},

			addProduct(product) {
				VueEvent.fire('addProduct', product);
			}
		}
	}

</script>