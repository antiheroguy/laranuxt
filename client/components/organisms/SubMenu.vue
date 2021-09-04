<template>
  <a-sub-menu
    v-if="menu.menus.length"
    :key="menu.id"
    v-bind="$props"
    v-on="$listeners"
  >
    <span slot="title">
      <menu-item :item="menu" />
    </span>
    <template v-for="item in menu.menus">
      <a-menu-item
        v-if="!item.menus.length"
        :key="item.id"
      >
        <menu-item :item="item" />
      </a-menu-item>
      <sub-menu
        v-else
        :key="item.id"
        :menu="item"
      />
    </template>
  </a-sub-menu>
  <a-menu-item
    v-else
    :key="menu.id"
  >
    <menu-item :item="menu" />
  </a-menu-item>
</template>

<script>
import { Menu } from 'ant-design-vue'
import MenuItem from '~/components/molecules/MenuItem'

export default {
  name: 'SubMenu',

  components: { MenuItem },

  data() {
    return {
      isSubMenu: true
    }
  },

  props: {
    ...Menu.SubMenu.props,

    menu: {
      type: Object,
      default: () => { }
    }
  }
}
</script>
