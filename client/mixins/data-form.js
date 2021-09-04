import { mapState } from 'vuex'
import { cloneDeep } from 'lodash'

export default {
  props: {
    id: {
      type: [Number, String],
      default: 0
    }
  },

  mounted() {
    this.getModel(this.id)
  },

  watch: {
    id(val) {
      this.getModel(val)
    }
  },

  data() {
    return {
      model: {}
    }
  },

  computed: {
    ...mapState({
      loading: 'loading'
    })
  },

  methods: {
    /**
     * Get item detail
     *
     * @param {Number} id
     */
    async getModel(id) {
      try {
        this.$store.dispatch('setLoading', true)
        if (this.$refs.form) {
          this.$refs.form.clearValidate()
        }
        await this.$store.dispatch(`${this.resource}/getModel`, { id })
        this.model = cloneDeep(this.$store.state[this.resource].model)
      } catch (_) {
        this.$notification.error({
          message: this.$t('text.something_wrong')
        })
      } finally {
        this.$store.dispatch('setLoading', false)
      }
    },

    /**
     * Validate before submit
     */
    handleSubmit() {
      this.$refs.form.validate(async valid => {
        if (valid) {
          try {
            this.$store.dispatch('setLoading', true)
            await this.$store.dispatch(`${this.resource}/saveModel`, this.model)

            this.$notification.success({
              message: this.$t('text.successfully')
            })

            this.$emit('save')
          } catch (_) {
            this.$notification.error({
              message: this.$t('text.something_wrong')
            })
          } finally {
            this.$store.dispatch('setLoading', false)
          }
        }
      })
    }
  }
}
