<template>
  <a-spin :spinning="loading" />
</template>

<script>
export default {
  layout: 'guest',

  middleware: 'guest',

  data: () => ({
    loading: true
  }),

  mounted() {
    window.opener.postMessage({ callback: this.$route.query })
    window.close()
  }
}
</script>
