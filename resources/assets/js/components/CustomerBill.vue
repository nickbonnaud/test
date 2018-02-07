<template>
	<div v-show="this.currentBill.length > 0" class="box box-black">
    <div class="box-header with-border">
      <h3 class="box-title">{{ this.customer.first_name }}'s Receipt</h3>
      <div class="pull-right">
      	<button class="btn btn-block btn-success btn-xs" v-on:click="saveBill(false)">Keep Open</button>
      </div>
    </div>
    <div class="box-body no-padding">
      <table class="table table-striped">
        <tbody>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Price</th>
            <th></th>
          </tr>
          <tr class="product-row" v-for="product in this.currentBill">
						<td class="product-row-data">{{ product.quantity }}</td>
						<td class="product-row-data">{{ product.name }}</td>
						<td class="product-row-data">${{ product.price / 100 }}</td>
						<td class="product-row-data"><span class="glyphicon glyphicon-minus-sign" v-on:click="subtractProduct(product)"></span></td>
					</tr>
        </tbody>
      </table>
    </div>
    <div class="box-footer-receipt">
      <div class="tax-section">
        <span>Tax:</span>
        <span class="pull-right">${{ (totalTax / 100).toFixed(2) }}</span>
      </div>
      <b>Total:</b>
      <div class="receipt-total">
        <b>${{ (totalBill / 100).toFixed(2) }}</b>
      </div>
    </div>
    <div>
      <button class="btn btn-block btn-success" v-on:click="saveBill(true)">Charge Customer</button>
    </div>
  </div>
</template>

<script>
	import swal from 'sweetalert2';

	export default {
		props: ['bill', 'customer', 'employeeId', 'profile'],

		data() {
			return {
				inventory: [],
				query: '',
				currentBill: []
			}
		},

		created() {
			this.setCurrentBill();
		},

		mounted() {
			VueEvent.listen('addProduct', this.addProduct.bind(this));

			Echo.private('bill-push-success.' + this.profile.slug)
        .listen('BillPushSuccess', (event) => {
          this.showPushSuccess(event);
        });
		},

		computed: {
     subTotal() {
        var total = 0;
        this.currentBill.forEach(function(product) {
          total = total + (product.quantity * product.price)
        });
        return total;
      },

      totalTax() {
        var tax = this.subTotal * this.profile.tax / 10000;
        console.log(tax);
        return tax;
      },

      totalBill() {
        var total = this.subTotal + this.totalTax;
        console.log(total);
        return total;
      }
    },

		methods: {

			setCurrentBill() {
				if (this.bill.products) {
					this.currentBill = JSON.parse(this.bill.products);
				}
			},

			addProduct(product) {
				product.price = product.price * 100;
				var index = this.getProductIndex(product);
				if (index == -1) {
					this.$set(product, 'quantity', 1);
					this.currentBill.push(product);
				} else {
					this.currentBill[index].quantity++;
				}
			},

			subtractProduct(product) {
				var index = this.getProductIndex(product);
				if (this.currentBill[index].quantity != 1) {
					this.currentBill[index].quantity--
				} else {
					this.currentBill.splice(index, 1);
				}
			},

			getProductIndex(product) {
				var index = this.currentBill.findIndex(function(selectedProduct) {
					return selectedProduct.id == this.id;
				}, product);
				return index;
			},

			saveBill(closeBill) {
				if (this.bill.id) {
					axios.patch('/api/web/transactions/' + this.profile.slug + '/' + this.bill.id, {
						'products': this.filterAttributes(),
						'tax': Math.round(this.totalTax),
						'net_sales': Math.round(this.subTotal),
						'total': Math.round(this.totalBill),
						'bill_closed': closeBill,
						'status': 10
					})
          .then(this.checkSuccess);
				} else {
					axios.post('/api/web/transactions/' + this.profile.slug + '/' + this.customer.id, {
						'products': this.filterAttributes(),
						'tax': Math.round(this.totalTax),
						'net_sales': Math.round(this.subTotal),
						'total': Math.round(this.totalBill),
						'employee_id': this.employeeId,
						'user_id': this.customer.id,
						'profile_id': this.profile.id,
						'bill_closed': closeBill,
						'status': 10
					})
          .then(this.checkSuccess);
				}
			},

			checkSuccess({data}) {
				console.log(data);
				if (!data.success) {
					swal({
						title: 'Oops! Something went wrong.',
						text: 'Transaction was not processed. Please try again. If error continues please contact Pockeyt.',
						type: 'error',
						showConfirmButton: true
					});
				}
			},

			showPushSuccess(data) {
				console.log(data);
				if (data.success) {
					swal({
						title: 'Success',
						text: 'Awaiting customer approval',
						type: 'success',
						timer: 1000,
						showConfirmButton: false
					}).then(
						function() {},
						function(dismiss) {
							return window.location.href = "/profiles/" + this.profile.slug;
						}.bind(this)
					)
				} else {
					swal({
						title: 'Oops! Something went wrong.',
						text: 'Unable to send bill to customer for approval. Please try again. If error continues please contact Pockeyt.',
						type: 'error',
						showConfirmButton: true
					});
				}
			},

			filterAttributes() {
				this.currentBill.forEach(function(item) {
					delete item.description;
					delete item.category;
					delete item.sku;
					delete item.photo;
					delete item.thumbnail;
				});
				return JSON.stringify(this.currentBill)
			}
		}
	}

</script>