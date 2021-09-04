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
            :md="20"
          >
            <a-form-model-item
              :label="$t('role.name')"
              prop="name"
            >
              <a-input
                v-model="model.name"
                :placeholder="$t('role.name')"
              >
                <font-awesome-icon
                  slot="prefix"
                  icon="heading"
                  class="width-1x"
                  style="color:rgba(0,0,0,.25)"
                />
              </a-input>
            </a-form-model-item>
          </a-col>

          <a-col
            :span="24"
            :md="20"
          >
            <a-form-model-item
              :label="$t('role.permissions')"
              prop="permissions"
            >
              <a-checkbox-group
                v-model="model.permissionIds"
                name="permissions"
              >
                <a-collapse
                  v-if="permissions.length"
                  expand-icon-position="right"
                >
                  <a-collapse-panel
                    v-for="group in permissions"
                    :key="`${group.name}`"
                    :header="group.name"
                  >
                    <div
                      v-for="permission in group.permissions"
                      :key="permission.id"
                    >
                      <a-checkbox :value="permission.id">
                        {{ permission.name }}
                      </a-checkbox>
                    </div>
                  </a-collapse-panel>
                </a-collapse>
              </a-checkbox-group>
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
      const { data: { data } } = await this.$api.getPermission()
      const parents = [...new Set(data.map(item => item.name.split('.')[0]))]

      this.permissions = parents.map(entry => {
        return {
          name: this.$t(`module.${entry}`),
          permissions: data.filter((item, index) => item.name.startsWith(`${entry}.`))
        }
      })
    } catch (_) {
      this.$notification.error({
        message: this.$t('text.something_wrong')
      })
    } finally {
      this.$store.dispatch('setLoading', false)
    }
  },

  data: () => ({
    resource: 'role',
    permissions: []
  }),

  computed: {
    formRules() {
      return {
        name: [
          {
            required: true,
            message: this.$t('validation.required', { field: this.$t('role.name') }),
            trigger: ['change', 'blur']
          }
        ]
      }
    }
  }
}
</script>
