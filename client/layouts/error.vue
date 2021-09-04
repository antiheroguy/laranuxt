<template>
  <a-result
    :status="errorCode"
    :title="errorCode"
    :sub-title="$t(`error.${errorCode}`)"
  >
    <template #extra>
      <a-button
        type="primary"
        @click="$router.push('/')"
      >
        {{ $t('common.home') }}
      </a-button>
    </template>
  </a-result>
</template>

<script>
export default {
  props: {
    error: {
      type: Object,
      default: () => { }
    }
  },

  computed: {
    errorCode() {
      let status = 500
      if ([403, 404].includes(this.error.statusCode)) {
        status = this.error.statusCode
      }

      return `${status}`
    }
  }
}
</script>
