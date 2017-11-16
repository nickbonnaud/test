Vue.component('products', {
	template: '#products-template',

	data: function() {
		return {
			inventory: []
		};
	},

	created: function() {
		var id = '{{ $business->id }}';
		console.log(id);
		$.getJSON('/products/inventory/{{ $business->id }}', function(data) {
			console.log(data);
		})
	}

});

new Vue({
	el: '#inventory'
});