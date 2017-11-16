<template>
</template>
<script>

	export default {

		mounted() {
			Echo.private('debug')
        .listen('Debug', (event) => {
          this.debugLog(event);
        });
		},

		methods: {
      debugLog: function(data) {
        console.log(data);
      }
		}
	}
</script>