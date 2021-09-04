<template>
  <a-spin :spinning="loading" />
</template>

<script>
export default {
  layout: 'blank',

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
