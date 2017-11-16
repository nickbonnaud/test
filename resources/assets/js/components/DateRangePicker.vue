<template>
	<input type="text" :id="id" :name="id" />
</template>

<script>
	export default {
		props: ['id'],

		mounted() {
			var input = $('input[name="'+ this.id +'"]');
			input.daterangepicker({
	    	timePicker: true,
	    	autoUpdateInput: true,
	      timePickerIncrement: 30,
	      locale: {
	        format: 'MM/DD/YYYY h:mm A'
	      }
	    });
	    input.on('apply.daterangepicker', function(ev, picker) {
	    	VueEvent.fire('dateRangeChanged', picker);
	    });
		}
	}
</script>