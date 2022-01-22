import { mapState } from 'vuex'

export default {
  props: {
    id: {
      type: [Number, String],
      default: 0
    }
  },

  mounted() {
    this.getDetail(this.id)
  },

  watch: {
    id(val) {
      this.getDetail(val)
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
    }),

    repository() {
      return this.$api[this.resource]
    }
  },

  methods: {
    /**
     * Get model
     *
     * @returns {Object}
     */
    getModel() {
      return this.model
    },

    /**
     * Set model
     *
     * @param {Object} data
     */
    setModel(data) {
      this.model = data
    },

    /**
     * Get item detail
     *
     * @param {Number} id
     */
    async getDetail(id) {
      this.$store.dispatch('setLoading', true)

      try {
        if (this.$refs.form) {
          this.$refs.form.clearValidate()
        }
        let model = {}
        if (id) {
          const { data: { data } } = await this.repository.show(id)
          model = data
        }
        this.setModel(model)
      } catch (error) {
        this.$notification.error({
          message: error?.response?.data?.message || this.$t('text.something_wrong')
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
          this.$store.dispatch('setLoading', true)

          try {
            const data = this.getModel()
            if (this.model.id) {
              await this.repository.update(this.model.id, data)
            } else {
              await this.repository.create(data)
            }

            this.$notification.success({
              message: this.$t('text.successfully')
            })

            this.$emit('save')
          } catch (error) {
            this.$notification.error({
              message: error?.response?.data?.message || this.$t('text.something_wrong')
            })
          } finally {
            this.$store.dispatch('setLoading', false)
          }
        }
      })
    }
  }
}
