<template>
  <a-form-model
    ref="form"
    :model="this"
    :rules="formRules"
    class="form__login"
    @submit.prevent="handleSubmit"
  >
    <a-spin :spinning="loading">
      <div class="container__logo">
        <div class="wrapper__logo">
          <div class="img__logo">
            <font-awesome-icon icon="user-tie" />
          </div>
        </div>

        <div class="content__login p-4">
          <h2 class="text-center mb-3">
            {{ $t('common.login') }}
          </h2>

          <a-form-model-item prop="email">
            <a-input
              v-model="email"
              :placeholder="$t('user.email')"
            >
              <font-awesome-icon
                slot="prefix"
                icon="envelope"
                class="width-1x"
                style="color:rgba(0,0,0,.25)"
              />
            </a-input>
          </a-form-model-item>

          <a-form-model-item prop="password">
            <a-input
              v-model="password"
              type="password"
              :placeholder="$t('user.password')"
            >
              <font-awesome-icon
                slot="prefix"
                icon="lock"
                class="width-1x"
                style="color:rgba(0,0,0,.25)"
              />
            </a-input>
          </a-form-model-item>

          <div
            v-if="message"
            class="ant-form-item has-error"
          >
            <span class="ant-form-explain">{{ message }}</span>
          </div>

          <div class="btn__login">
            <a-button
              html-type="submit"
              type="primary"
              size="large"
              class="min-w-100"
            >
              {{ $t('common.login') }}
            </a-button>
            <google-button />
          </div>
        </div>
      </div>
    </a-spin>
  </a-form-model>
</template>

<script>
import { mapState } from 'vuex'
import { REFRESH_TOKEN } from '~/constants/cookies'
import GoogleButton from '~/components/atoms/GoogleButton'

export default {
  layout: 'guest',

  components: {
    GoogleButton
  },

  middleware: 'guest',

  data() {
    return {
      email: '',
      password: '',
      message: ''
    }
  },

  computed: {
    ...mapState({
      loading: 'loading'
    }),

    formRules() {
      return {
        email: [
          {
            required: true,
            message: this.$t('validation.required', { field: this.$t('user.email') }),
            trigger: ['change', 'blur']
          }
        ],
        password: [
          {
            required: true,
            message: this.$t('validation.required', { field: this.$t('user.password') }),
            trigger: ['change', 'blur']
          }
        ]
      }
    }
  },

  methods: {
    /**
     * Handle submit
     */
    handleSubmit() {
      this.$refs.form.validate(async valid => {
        if (valid) {
          this.$store.dispatch('setLoading', true)

          try {
            const credential = {
              email: this.email,
              password: this.password
            }
            const { data } = await this.$auth.login({ data: credential })
            if (data.refresh_token) {
              this.$cookies.set(REFRESH_TOKEN, data.refresh_token, { maxAge: process.env.LONG_LIVED_TOKEN_LIFETIME, path: '/' })
            }
            this.$router.push({ path: '/' })
          } catch (_) {
            this.message = this.$t('validation.not_match', { field1: this.$t('user.email'), field2: this.$t('user.password') })
          } finally {
            this.$store.dispatch('setLoading', false)
          }
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.form__login {
  /deep/ {
    width: 100%;
    max-width: 400px;
    background: #fff;
    box-shadow: 0 0 10px 1px rgba(0, 0, 0, 0.2);
    margin: 30px 0;
    .container__logo {
      padding-bottom: 30px;
      .wrapper__logo {
        text-align: center;
        background: $primary-color;
        padding: 30px 0 80px;
      }
      .img__logo {
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 80px;
        line-height: 1;
        border-radius: 50%;
        margin: auto;
      }
    }
    .content__login {
      position: relative;
      top: -40px;
      width: 80%;
      box-shadow: 2px 5px 16px 2px rgba(16, 16, 16, 0.18);
      background: #fff;
      border-radius: 6px;
      margin: auto;
      z-index: 2;
      .btn__login {
        display: flex;
        align-items: center;
        justify-content: space-around;
      }
    }
  }
}
</style>
