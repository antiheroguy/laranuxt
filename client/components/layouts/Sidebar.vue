<template>
  <a-layout-sider
    v-model="isCollapsed"
    collapsible
    width="256"
    :collapsed-width="collapsedWidth"
  >
    <div class="logo">
      <nuxt-link to="/">
        <img
          src="/images/logo.png"
          alt="Logo"
        />
      </nuxt-link>
    </div>
    <!-- end logo -->

    <a-menu
      theme="dark"
      mode="inline"
      :selected-keys="selectedKeys"
      :default-open-keys="selectedKeys"
    >
      <template v-for="item in menu">
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
    </a-menu>
  </a-layout-sider>
</template>

<script>
import { debounce, get, trim } from 'lodash'
import { mapState, mapMutations } from 'vuex'
import SubMenu from '~/components/organisms/SubMenu'
import MenuItem from '~/components/molecules/MenuItem'
import { SET_IS_SIDEBAR_COLLAPSED, SET_IS_PC } from '~/constants/mutation-types'

export default {
  components: {
    SubMenu,
    MenuItem
  },

  computed: {
    ...mapState({
      isPC: 'isPC',
      isSidebarCollapsed: 'isSidebarCollapsed'
    }),

    isCollapsed: {
      get() {
        return this.isSidebarCollapsed
      },
      set(value) {
        this.setIsSidebarCollapsed(value)
      }
    },

    collapsedWidth() {
      return this.isPC ? 80 : 0
    },

    menu() {
      const staticMenu = [
        {
          icon: 'home',
          id: 0,
          link: '/',
          menus: [],
          parent_id: 0,
          position: 0,
          title: 'common.home'
        }
      ]
      const dynamicMenu = get(this, '$auth.user.menus', [])
      const menu = [...staticMenu, ...dynamicMenu]
      menu.sort((a, b) => a.position - b.position)
      return menu
    },

    selectedKeys() {
      const selectedKeys = []
      const deepFind = (menu, parent = null) => {
        menu.forEach(item => {
          if (trim(item.link, '/') === trim(this.$route.path, '/')) {
            if (parent) {
              selectedKeys.push(parent.id)
            }
            selectedKeys.push(item.id)
          }
          if (item.menus) {
            deepFind(item.menus, item)
          }
        })
      }
      deepFind(this.menu)
      return selectedKeys
    }
  },

  mounted() {
    this.checkIsPc()

    let resizeTimer
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer)

      resizeTimer = setTimeout(
        debounce(() => this.checkIsPc()),
        250
      )
    })
  },

  methods: {
    ...mapMutations({
      setIsSidebarCollapsed: SET_IS_SIDEBAR_COLLAPSED,
      setIsPC: SET_IS_PC
    }),

    /**
     * Checking is PC
     */
    checkIsPc() {
      const isPC = Math.max(document.documentElement.clientWidth, window.innerWidth || 0) >= 992
      this.setIsPC(isPC)
      this.isCollapsed = !isPC
    }
  }
}
</script>

<style scoped lang="scss">
/deep/ {
  .ant-layout-sider-zero-width-trigger {
    display: none;
  }
}
.ant-layout-sider {
  .logo {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 64px;
    background: #002140;
    text-align: center;
    padding: 6px 10px;
    a {
      text-decoration: none;
    }
    img {
      max-height: 45px;
    }
  }
  &.ant-layout-sider-collapsed {
    .ant-menu-item-group-title {
      display: none;
    }
    .ant-menu {
      .ant-menu-item {
        > a {
          display: block;
        }
      }
    }
  }
  .ant-menu {
    .ant-menu-item {
      > a {
        display: block;
        &.active-link,
        &[href="/"]:not(:hover) {
          background: $primary-color;
          &:not(.on-home) {
            background: transparent;
          }
        }
      }
    }
  }
}
</style>
