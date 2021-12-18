<template>
  <div>
    <a-card class="mb-4">
      <template slot="title">
        {{ $t('module.user') }}
      </template>

      <template slot="extra">
        <a-button
          html-type="button"
          type="primary"
          ghost
          @click="gotoNew()"
        >
          <font-awesome-icon
            icon="plus-circle"
            class="mr-1"
          />
          {{ $t('common.new') }}
        </a-button>
      </template>

      <a-form-model
        ref="form"
        :model="filters"
        :label-col="{ sm: 6 }"
        :wrapper-col="{ sm: 18 }"
        class="mb-4"
        @submit.prevent="search"
      >
        <a-row
          type="flex"
          :gutter="30"
        >
          <a-col
            :span="24"
            :md="12"
          >
            <a-form-model-item
              :label="$t('user.name')"
              prop="name"
            >
              <a-input
                v-model="filters.name"
                :placeholder="$t('user.name')"
                :disabled="loading"
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
              :label="$t('user.email')"
              prop="email"
            >
              <a-input
                v-model="filters.email"
                :placeholder="$t('user.email')"
                :disabled="loading"
              >
                <font-awesome-icon
                  slot="prefix"
                  icon="envelope"
                  style="color:rgba(0,0,0,.25)"
                />
              </a-input>
            </a-form-model-item>
          </a-col>

          <a-col
            :md="24"
            class="text-center"
          >
            <a-button
              html-type="submit"
              type="primary"
              class="min-w-100"
              :disabled="loading"
            >
              <font-awesome-icon
                icon="search"
                class="mr-1"
              />
              {{ $t('common.search') }}
            </a-button>

            &nbsp;
            <a-button
              html-type="button"
              type="default"
              class="min-w-100"
              :disabled="loading"
              @click="reset"
            >
              <font-awesome-icon
                icon="eraser"
                class="mr-1"
              />
              {{ $t('common.clear') }}
            </a-button>
          </a-col>
        </a-row>
      </a-form-model>
      <!-- end main-search -->

      <a-table
        :columns="columns"
        :row-key="record => record.id"
        :data-source="data"
        :pagination="pagination"
        :scroll="{ x: 400 }"
        :loading="loading"
        @change="handleTableChange"
      >
        <template
          slot="roles"
          slot-scope="text, record"
        >
          {{ getRolesName(record.roles) }}
        </template>

        <template
          slot="action"
          slot-scope="text, record"
        >
          <a-button
            html-type="button"
            type="primary"
            size="small"
            :disabled="loading"
            @click="gotoDetail(record.id)"
          >
            <font-awesome-icon icon="pencil-alt" />
          </a-button>

          <a-button
            html-type="button"
            type="primary"
            size="small"
            :disabled="loading"
            @click="showDetail(record.id)"
          >
            <font-awesome-icon icon="eye" />
          </a-button>

          <a-button
            html-type="button"
            type="danger"
            size="small"
            :disabled="loading"
            @click="confirmToDelete(record.id)"
          >
            <font-awesome-icon icon="trash-alt" />
          </a-button>
        </template>
      </a-table>
      <!-- end main-table -->
    </a-card>

    <a-modal
      ref="detail"
      :visible="visible"
      :width="1300"
      :footer="null"
      @cancel="visible = false"
    >
      <template slot="title">
        {{ currentId ? $t('common.edit') : $t('common.create') }} {{ $t('module.user') }}
      </template>

      <a-spin :spinning="loading">
        <user-form
          :id="currentId"
          @save="closeDialog(true)"
          @cancel="closeDialog(false)"
        />
      </a-spin>
    </a-modal>
    <!-- end modal-detail -->
  </div>
</template>

<script>
import UserForm from '~/components/templates/UserForm'
import DataTable from '~/mixins/data-table'

export default {
  components: {
    UserForm
  },

  mixins: [
    DataTable
  ],

  data() {
    return {
      resource: 'user',
      visible: false,
      currentId: 0,
      filters: {
        name: this.$route.query.name || '',
        email: this.$route.query.email || ''
      }
    }
  },

  computed: {
    header() {
      return [
        {
          title: 'ID',
          dataIndex: 'id',
          fixed: 'left',
          width: 60
        },
        {
          title: this.$t('user.name'),
          dataIndex: 'name',
          sorter: true
        },
        {
          title: this.$t('user.email'),
          sorter: true,
          dataIndex: 'email'
        },
        {
          title: this.$t('user.roles'),
          dataIndex: 'roles',
          scopedSlots: { customRender: 'roles' }
        },
        {
          title: this.$t('common.action'),
          dataIndex: 'action',
          scopedSlots: { customRender: 'action' },
          width: 140
        }
      ]
    }
  },

  methods: {
    /**
     * Get roles name
     *
     * @param {array} roles - Role list
     * @return {string} role list
     */
    getRolesName(roles) {
      return roles.map(role => role.name).join(', ')
    },

    /**
     * Show detail
     *
     * @param {number} id - Item Id
     */
    showDetail(id) {
      this.currentId = id
      this.visible = true
    },

    /**
     * Close dialog
     *
     * @param {boolean} fetch - fetch status
     */
    closeDialog(fetch) {
      this.visible = false

      if (fetch) {
        this.$fetch()
      }
    },

    /**
     * Clear form search
     */
    reset() {
      this.filters = {
        name: '',
        email: ''
      }
    },

    /**
     * Search data
     */
    search() {
      this.replaceQuery(this.filters)
    }
  }
}
</script>
