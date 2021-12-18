<template>
  <a-form-model
    ref="form"
    :model="model"
    :rules="formRules"
    :label-col="{ sm: 6 }"
    :wrapper-col="{ sm: 18 }"
    @submit.prevent="handleSubmit"
  >
    <a-spin :spinning="loading">
      <div class="p-4">
        <a-row
          type="flex"
          :gutter="30"
        >
          <a-col
            :span="24"
            :md="12"
          >
            <a-form-model-item
              :label="$t('menu.title')"
              prop="title"
            >
              <a-input
                v-model="model.title"
                :placeholder="$t('menu.title')"
              >
                <font-awesome-icon
                  slot="prefix"
                  icon="heading"
                  style="color:rgba(0,0,0,.25)"
                />
              </a-input>
            </a-form-model-item>
          </a-col>

          <a-col
            :span="24"
            :md="12"
          >
            <a-form-model-item
              :label="$t('menu.icon')"
              prop="icon"
            >
              <a-input
                v-model="model.icon"
                :placeholder="$t('menu.icon')"
              >
                <font-awesome-icon
                  slot="prefix"
                  icon="icons"
                  style="color:rgba(0,0,0,.25)"
                />
              </a-input>
            </a-form-model-item>
          </a-col>

          <a-col
            :span="24"
            :md="12"
          >
            <a-form-model-item
              :label="$t('menu.link')"
              prop="link"
            >
              <a-input
                v-model="model.link"
                :placeholder="$t('menu.link')"
              >
                <font-awesome-icon
                  slot="prefix"
                  icon="link"
                  style="color:rgba(0,0,0,.25)"
                />
              </a-input>
            </a-form-model-item>
          </a-col>

          <a-col
            :span="24"
            :md="12"
          >
            <a-form-model-item
              :label="$t('menu.roles')"
              prop="roles"
            >
              <a-select
                v-model="model.role_ids"
                mode="multiple"
                :placeholder="$t('menu.roles')"
              >
                <a-select-option
                  v-for="role in roles"
                  :key="role.id"
                >
                  {{ role.name }}
                </a-select-option>
              </a-select>
            </a-form-model-item>
          </a-col>
        </a-row>
      </div>

      <div class="text-center p-3">
        <a-button
          html-type="submit"
          type="primary"
          class="min-w-100"
        >
          {{ id ? $t('common.update') : $t('common.create') }}
        </a-button>

        &nbsp;
        <a-button
          html-type="button"
          type="default"
          class="min-w-100"
          @click="$emit('cancel')"
        >
          {{ $t('common.cancel') }}
        </a-button>
      </div>
    </a-spin>
  </a-form-model>
</template>

<script>
import DataForm from '~/mixins/data-form'

export default {
  mixins: [DataForm],

  async fetch() {
    this.$store.dispatch('setLoading', true)

    try {
      const { data: { data } } = await this.$api.role.list({ params: { all: true } })
      this.roles = data
    } catch (_) {
      this.$notification.error({
        message: this.$t('text.something_wrong')
      })
    } finally {
      this.$store.dispatch('setLoading', false)
    }
  },

  data: () => ({
    resource: 'menu',
    roles: []
  }),

  computed: {
    formRules() {
      return {
        title: [
          {
            required: true,
            message: this.$t('validation.required', { field: this.$t('menu.title') }),
            trigger: ['change', 'blur']
          }
        ]
      }
    }
  },

  methods: {
    /**
     * Get model
     *
     * @returns {Object}
     */
    getModel() {
      const model = {
        ...this.model,
        roles: this.model.role_ids
      }
      return model
    },

    /**
     * Set model
     *
     * @param {Object} data
     */
    setModel(data) {
      data.role_ids = data.roles ? data.roles.map(item => item.id) : []
      this.model = data
    }
  }
}
</script>
