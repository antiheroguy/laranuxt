<template>
  <a-breadcrumb class="mb-3">
    <a-breadcrumb-item
      v-for="(item, index) in crumbs"
      :key="index"
    >
      <nuxt-link :to="item.to">
        {{ item.text }}
      </nuxt-link>
    </a-breadcrumb-item>
  </a-breadcrumb>
</template>

<script>
export default {
  computed: {
    crumbs() {
      const crumbs = [
        { to: '/', text: this.$t('common.home') }
      ]

      this.$route.path.split('/').filter(item => item).reduce((previous, current) => {
        const crumb = {
          to: `${previous}/${current}`,
          text: this.$t(current)
        }
        crumbs.push(crumb)
        return crumb.to
      }, '')

      return crumbs
    }
  }
}
</script>
