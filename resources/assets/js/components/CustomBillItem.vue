<template>
	<div class="modal fade" id="customItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header-timeline">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title" id="customItem">Custom Amount</h3>
        </div>
        <div class="modal-body-custom-amount">
          <section class="content custom-amount">
            <div class="col-md-12">
              <form class="form-horizontal">
                <div class="form-group" style="margin-left: 15%;">
                  <label for="inputName" class="col-sm-2 control-label">Name</label>
                  <div class="col-sm-10">
                    <input v-model="name" name="name" type="text" class="form-control" style="width: 50%;" id="inputName" placeholder="Name" required>
                  </div>
                </div>
                <div class="form-group" style="margin-left: 15%;">
                  <label for="price" class="col-sm-2 control-label">Price</label>
                  <div class="col-sm-10">
                    <input-money type="price"></input-money>
                  </div>
                </div>
                <button v-bind:disabled="(name == '' || price == '')" type="button" class="btn btn-block btn-primary" v-on:click="addCustomProduct()">Add</button>
              </form>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
	export default {

		data() {
			return {
				name: '',
				price:''
			}
		},

		mounted() {
			VueEvent.listen('priceChange', this.setPrice.bind(this));
		},

		methods: {
			addCustomProduct() {
				var product = {
					id: 'custom' + this.name,
					name: this.name,
					price: this.price,
					quantity: 1 
				}
				VueEvent.fire('addProduct', product);
				this.name = '';
        this.price = '';
        $('#customItem').modal('hide');
			},

			setPrice(price) {
				this.price = price;
			}
		}
	}
</script>