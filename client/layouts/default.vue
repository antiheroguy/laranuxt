<template>
  <a-config-provider :locale="locale">
    <a-layout class="layout__container">
      <Sidebar />
      <!-- end sidebar menu -->

      <a-layout>
        <Header />
        <!-- end main header -->

        <a-layout-content class="pt-4">
          <transition name="fade">
            <div class="layout__content">
              <Breadcrumb />
              <!-- end breadcrumb -->

              <Nuxt />
              <!-- end main content -->
            </div>
          </transition>
        </a-layout-content>

        <Footer />
        <!-- end main footer -->
      </a-layout>
      <!-- end main sidebar -->
    </a-layout>
  </a-config-provider>
</template>

<script>
import Footer from '~/components/layouts/Footer'
import Header from '~/components/layouts/Header'
import Sidebar from '~/components/layouts/Sidebar'
import Breadcrumb from '~/components/layouts/Breadcrumb'

export default {
  components: {
    Footer,
    Header,
    Sidebar,
    Breadcrumb
  },

  middleware: ['auth'],

  computed: {
    locale() {
      const locale = this.$i18n.locales.find(item => item.code === this.$i18n.locale)
      const localeISO = locale ? locale.iso : 'en-US'
      const provider = require(`ant-design-vue/es/locale-provider/${localeISO.replace('-', '_')}`)
      return provider.default
    }
  }
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
.layout__container {
  min-height: 100vh;
  .layout__content {
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
    @include mq(md) {
      padding-right: 30px;
      padding-left: 30px;
    }
  }
}
</style>
