<template>
</template>
<script>

	export default {

		mounted() {
      console.log("hello");
			Echo.channel('debug')
        .listen('Debug', (event) => {
          console.log(event);
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