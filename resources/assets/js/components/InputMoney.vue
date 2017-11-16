<template>
  <div>
    <money v-model="price" v-bind="money" type="tel" :name="type" class="form-control" :id="type" required></money>
  </div>
</template>

<script>
  import {Money} from 'v-money'
	
  export default {
		components: {Money},
    props: ['value', 'type'],

		data() {
      return {
        price: this.value,
        money: {
          decimal: '.',
          thousands: ',',
          prefix: '$ ',
          precision: 2,
          masked: false
        }
      }
    },
    watch: {
      price: function () {
        VueEvent.fire('priceChange', this.price);
      }
    }
	}
</script>