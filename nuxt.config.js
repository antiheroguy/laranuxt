import { join } from 'path'
import { copySync, removeSync } from 'fs-extra'
import icons from './client/constants/icons'

require('dotenv').config()

export default {
  // Disable server-side rendering: https://go.nuxtjs.dev/ssr-mode
  ssr: false,

  // Target: https://go.nuxtjs.dev/config-target
  target: 'static',

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Src directory: https://nuxtjs.org/docs/2.x/configuration-glossary/configuration-srcdir
  srcDir: 'client/',

  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    title: 'Laranuxt',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { name: 'description', content: 'Laranuxt' },
      { name: 'og:title', content: 'Laranuxt' },
      { name: 'og:site_name', content: 'Laranuxt' },
      { name: 'og:description', content: 'Laranuxt' },
      { name: 'apple-mobile-web-app-title', content: 'Laranuxt' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [
    { src: '~/assets/less/theme.less', lang: 'less' },
    { src: '~/assets/scss/style.scss', lang: 'scss' }
  ],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [
    '~/plugins/antd',
    '~/plugins/api',
    '~/plugins/directive'
  ],

  // Router Configuration: https://nuxtjs.org/docs/2.x/configuration-glossary/configuration-router
  router: {
    linkActiveClass: 'active-link'
  },

  // Define the custom directories: https://nuxtjs.org/docs/2.x/configuration-glossary/configuration-dir
  dir: {
    static: '../public'
  },

  // Environment variables for client side: https://nuxtjs.org/docs/2.x/configuration-glossary/configuration-env
  env: {
    LONG_LIVED_TOKEN_LIFETIME: process.env.LONG_LIVED_TOKEN_LIFETIME
  },

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {
    loaders: {
      less: {
        lessOptions: {
          javascriptEnabled: true
        }
      }
    }
  },

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    // https://go.nuxtjs.dev/eslint
    '@nuxtjs/eslint-module',
    // https://github.com/nuxt-community/fontawesome-module
    '@nuxtjs/fontawesome',
    // https://github.com/nuxt-community/moment-module
    '@nuxtjs/moment'
  ],

  // Fontawesome configuration: https://go.nuxtjs.dev/pwa
  fontawesome: {
    icons
  },

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    // https://auth.nuxtjs.org
    '@nuxtjs/auth',
    // https://go.nuxtjs.dev/axios
    '@nuxtjs/axios',
    // https://github.com/nuxt-community/dotenv-module
    '@nuxtjs/dotenv',
    // https://i18n.nuxtjs.org
    '@nuxtjs/i18n',
    // https://go.nuxtjs.dev/pwa
    '@nuxtjs/pwa',
    // https://github.com/nuxt-community/style-resources-module
    '@nuxtjs/style-resources',
    // https://github.com/microcipcip/cookie-universal/tree/master/packages/cookie-universal-nuxt
    'cookie-universal-nuxt'
  ],

  // Auth module configuration: https://auth.nuxtjs.org
  auth: {
    localStorage: false,
    redirect: {
      login: '/login',
      logout: '/login',
      home: false
    },
    strategies: {
      local: {
        endpoints: {
          login: { url: '/login', method: 'post', propertyName: 'access_token' },
          user: { url: '/me', method: 'get', propertyName: 'data' },
          logout: { url: '/logout', method: 'post' }
        }
      }
    },
    cookie: {
      options: {
        maxAge: process.env.LONG_LIVED_TOKEN_LIFETIME
      }
    },
    plugins: [
      '~/plugins/api.js'
    ]
  },

  // Axios module configuration: https://go.nuxtjs.dev/config-axios
  axios: {
    baseURL: process.env.API_BASE_URL
  },

  // i18n module configuration: https://i18n.nuxtjs.org/
  i18n: {
    locales: [
      { code: 'en', iso: 'en-US' },
      { code: 'vi', iso: 'vi-VN' }
    ],
    defaultLocale: 'en',
    strategy: 'no_prefix',
    vueI18n: {
      fallbackLocale: 'en',
      messages: {
        en: require('./client/locales/en.json'),
        vi: require('./client/locales/vi.json')
      },
      silentTranslationWarn: true
    }
  },

  // PWA module configuration: https://go.nuxtjs.dev/pwa
  pwa: {
    manifest: {
      lang: 'en'
    }
  },

  // Style resources module configuration: https://github.com/nuxt-community/style-resources-module#readme
  styleResources: {
    scss: [
      '~/assets/scss/_variables.scss',
      '~/assets/scss/_mixins.scss'
    ]
  },

  // Listeners to Nuxt events: https://nuxtjs.org/docs/2.x/configuration-glossary/configuration-hooks
  hooks: {
    generate: {
      done(generator) {
        const options = generator.nuxt.options
        if (options.dev === false && options.mode === 'spa') {
          const publicPath = options.build.publicPath
          const publicDir = join(options.rootDir, 'public', publicPath)
          removeSync(publicDir)
          copySync(join(options.generate.dir, publicPath), publicDir)
          copySync(join(options.generate.dir, '200.html'), join(publicDir, 'index.html'))
          removeSync(options.generate.dir)
        }
      }
    }
  }
}
