<template>
  <a-row
    type="flex"
    justify="center"
    align="middle"
    class="layout__row"
  >
    <transition name="fade">
      <Nuxt />
    </transition>
  </a-row>
</template>

<script>
export default {
  middleware: ['guest']
}
</script>

<style lang="scss" scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
.layout__row {
  min-height: 100vh !important;
}
</style>
