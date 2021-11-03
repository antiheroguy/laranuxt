<!-- eslint-disable no-tabs -->
<template>
  <div
    class="btn__google min-w-100"
    @click="redirect($t('brand.google'))"
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 48 48"
      width="18px"
      height="18px"
    >
      <path
        fill="#fbc02d"
        d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12	s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20	s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"
      />
      <path
        fill="#e53935"
        d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039	l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"
      />
      <path
        fill="#4caf50"
        d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36	c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"
      />
      <path
        fill="#1565c0"
        d="M43.611,20.083L43.595,20L42,20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571	c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"
      />
    </svg>
    <div class="text__google">
      {{ $t('brand.google') }}
    </div>
  </div>
</template>

<script>
import { REFRESH_TOKEN } from '~/constants/cookies'

export default {
  name: 'GoogleButton',

  mounted() {
    window.addEventListener('message', this.handleCallback, false)
  },

  beforeDestroy() {
    window.removeEventListener('message', this.handleCallback)
  },

  methods: {
    /**
     * Redirect to provider uri
     *
     * @param {String} title
     */
    async redirect(title) {
      this.$store.dispatch('setLoading', true)

      try {
        const params = { provider: 'google' }
        const { data: { url } } = await this.$api.oauth.getRedirectURI({ params })

        const options = { url, title, width: 600, height: 720 }
        const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screen.left
        const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screen.top
        const width = window.innerWidth || document.documentElement.clientWidth || window.screen.width
        const height = window.innerHeight || document.documentElement.clientHeight || window.screen.height
        options.left = (width / 2) - (options.width / 2) + (dualScreenLeft)
        options.top = (height / 2) - (options.height / 2) + dualScreenTop
        const optionsStr = Object.keys(options).reduce((acc, key) => [`${key}=${options[key]}`, ...acc], []).join(',')
        const newWindow = window.open(url, title, optionsStr)
        if (window.focus) {
          newWindow.focus()
        }

        newWindow.location.href = url
      } catch (e) {
        console.error(e)
      } finally {
        this.$store.dispatch('setLoading', false)
      }
    },

    /**
     * Callback for provider
     *
     * @param {MessageEvent} e
     */
    async handleCallback(e) {
      if (e.origin !== window.origin || !e.data.callback) {
        return
      }

      this.$store.dispatch('setLoading', true)

      try {
        const { data } = await this.$api.oauth.handleCallback({ provider: 'google', ...e.data.callback })
        await this.$auth.setUserToken(data.access_token)
        if (data.refresh_token) {
          this.$cookies.set(REFRESH_TOKEN, data.refresh_token, { maxAge: process.env.LONG_LIVED_TOKEN_LIFETIME, path: '/' })
        }
        this.$router.push({ path: '/' })
      } catch (e) {
        console.error(e)
      } finally {
        this.$store.dispatch('setLoading', false)
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.btn__google {
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border: 1px solid #c8c8c8;
  border-radius: 4px;
  height: 40px;
  padding: 0 15px;
  .text__google {
    margin-left: 5px;
    font-weight: bold;
  }
}
</style>
