<template>
  <nuxt-link
    v-if="item.link"
    :to="item.link"
  >
    <font-awesome-icon
      v-if="item.icon"
      :icon="item.icon"
      class="anticon"
    />
    <span>{{ $t(item.title) }}</span>
  </nuxt-link>

  <span v-else>
    <font-awesome-icon
      v-if="item.icon"
      :icon="item.icon"
      class="anticon"
    />
    <span>{{ $t(item.title) }}</span>
  </span>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      default: () => { }
    }
  }
}
</script>
