<template>
  <a-layout-header>
    <div class="header--left">
      <a
        href="javascript:void(0)"
        class="btn__header--collapse"
        @click="toggleIsCollapsed"
      >
        <a-icon :type="isCollapsed ? 'menu-unfold' : 'menu-fold'" />
      </a>
    </div>

    <div class="header--right pr-4">
      <a-dropdown
        overlay-class-name="dropdown__locale"
        :trigger="['click']"
      >
        <a
          class="link__dropdown"
          @click="e => e.preventDefault()"
        >
          <img
            :src="`/images/flag_${currentLocaleISO}.png`"
            width="28"
          />
          <font-awesome-icon icon="caret-down" />
        </a>

        <a-menu slot="overlay">
          <a-menu-item
            v-for="locale in $i18n.locales"
            :key="locale.code"
          >
            <a
              href="javascript:void(0)"
              @click="$i18n.setLocale(locale.code)"
            >
              <img :src="`/images/flag_${locale.iso}.png`" />
            </a>
          </a-menu-item>
        </a-menu>
      </a-dropdown>
      <a-dropdown
        overlay-class-name="dropdown__account"
        :trigger="['click']"
      >
        <a
          class="link__dropdown"
          @click.prevent
        >
          <font-awesome-icon
            icon="user-cog"
            class="font-awesome--large"
          />
        </a>

        <a-menu slot="overlay">
          <a-menu-item
            key="0"
            class="menu__profile"
          >
            {{ $auth.user ? $auth.user.email : '' }}
          </a-menu-item>
          <a-menu-item key="1">
            <a
              href="javascript:void(0)"
              @click="logout()"
            >
              <font-awesome-icon
                icon="sign-out-alt"
                class="width-1x"
              />&nbsp;
              {{ $t('common.logout') }}
            </a>
          </a-menu-item>
        </a-menu>
      </a-dropdown>
    </div>
  </a-layout-header>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import { REFRESH_TOKEN } from '~/constants/cookies'
import { SET_IS_SIDEBAR_COLLAPSED } from '~/constants/mutation-types'

export default {
  computed: {
    ...mapState({
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

    currentLocaleISO() {
      const locale = this.$i18n.locales.find(item => item.code === this.$i18n.locale)
      return locale ? locale.iso : 'en-US'
    }
  },

  methods: {
    ...mapMutations({
      setIsSidebarCollapsed: SET_IS_SIDEBAR_COLLAPSED
    }),

    /**
     * Logout user
     */
    async logout() {
      this.$cookies.remove(REFRESH_TOKEN, { path: '/' })
      this.$store.dispatch('setLoading', true)

      try {
        await this.$auth.logout()
      } catch (_) {
        this.$notification.error({
          message: this.$t('text.something_wrong')
        })
      } finally {
        this.$store.dispatch('setLoading', false)
      }
    },

    /**
     * Toggle collapsed status
     */
    toggleIsCollapsed() {
      this.isCollapsed = !this.isCollapsed
    }
  }
}
</script>

<style scoped lang="scss">
.dropdown__locale {
  min-width: 50px;
  .ant-dropdown-menu {
    border: 1px solid #d8dbe0;
  }
  .ant-dropdown-menu-item {
    text-align: center;
  }
}

.dropdown__account {
  min-width: 160px;
  .ant-dropdown-menu {
    border: 1px solid #d8dbe0;
    padding-top: 0;
  }
}

.menu__profile {
  background: #ebedef;
  text-align: center;
  cursor: default;
  pointer-events: none;
}

.ant-layout-header {
  display: flex;
  justify-content: space-between;
  line-height: 1.5;
  background: #fff;
  padding: 0;
  .header--left {
    display: flex;
    align-items: center;
  }
  .btn__header--collapse {
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 18px;
    padding: 0 24px;
    cursor: pointer;
    color: #4c4c4c;
    transition: all 0.4s ease;
    &:hover {
      color: $primary-color;
    }
  }
  .header--right {
    display: flex;
    align-items: center;
    > * {
      margin-left: 10px;
    }
    .link__dropdown {
      display: inline-block;
      vertical-align: middle;
      padding: 5px;
      color: #4c4c4c;
      &:hover {
        color: $primary-color;
      }
    }
    .font-awesome--large {
      font-size: 25px;
    }
  }
}
</style>
