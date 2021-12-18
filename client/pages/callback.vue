<template>
  <a-spin :spinning="true" />
</template>

<script>
export default {
  layout: 'guest',

  middleware: 'guest',

  mounted() {
    window.opener.postMessage({ callback: this.$route.query })
    window.close()
  }
}
</script>
