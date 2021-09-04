<template>
  <div>
    <a-card class="mb-4">
      <template slot="title">
        {{ $t('module.menu') }}
      </template>

      <template slot="extra">
        <a-button
          html-type="button"
          type="primary"
          ghost
          @click="showDetail(0)"
        >
          <font-awesome-icon
            icon="plus-circle"
            class="width-1x mr-1"
          />
          {{ $t('common.new') }}
        </a-button>
      </template>

      <div class="tree__menu">
        <a-spin :spinning="loading">
          <a-tree
            draggable
            :tree-data="data"
            @drop="onDrop"
          >
            <template
              slot="action"
              slot-scope="{ title, key, children }"
            >
              <span class="menu-tree__title">
                {{ $t(title) }}
              </span>

              <div class="menu-tree__action">
                <a-button
                  html-type="button"
                  type="primary"
                  size="small"
                  :disabled="loading"
                  @click="showDetail(key)"
                >
                  <font-awesome-icon
                    icon="pencil-alt"
                    class="width-1x"
                  />
                </a-button>

                <a-button
                  v-if="!children.length"
                  html-type="button"
                  type="danger"
                  size="small"
                  :disabled="loading"
                  @click="confirmToDelete(key)"
                >
                  <font-awesome-icon
                    icon="trash-alt"
                    class="width-1x"
                  />
                </a-button>
              </div>
            </template>
          </a-tree>
        </a-spin>
      </div>
    </a-card>

    <a-modal
      ref="detail"
      :visible="visible"
      :width="1300"
      :footer="null"
      @cancel="visible = false"
    >
      <template slot="title">
        {{ currentId ? $t('common.edit') : $t('common.create') }} {{ $t('module.menu') }}
      </template>

      <a-spin :spinning="loading">
        <menu-form
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
import { cloneDeep } from 'lodash'
import { mapState } from 'vuex'
import MenuForm from '~/components/templates/MenuForm'

export default {
  components: {
    MenuForm
  },

  async fetch() {
    this.$store.dispatch('setLoading', true)
    try {
      const { data } = await this.$api.indexMenu()
      const recursive = (parentId = 0) => {
        const list = cloneDeep(data.data.filter(item => item.parent_id === parentId))
        list.sort((a, b) => a.position - b.position)
        return list.map(item => {
          const children = recursive(item.id)
          return {
            key: item.id,
            title: item.title,
            scopedSlots: {
              title: 'action'
            },
            parent: item.parent_id,
            children
          }
        })
      }
      this.data = recursive()
      this.$auth.fetchUser()
    } catch (_) {
      this.$notification.error({
        message: this.$t('text.something_wrong')
      })
    } finally {
      this.$store.dispatch('setLoading', false)
    }
  },

  data() {
    return {
      resource: 'menu',
      visible: false,
      currentId: 0,
      data: [],
      list: []
    }
  },

  computed: {
    ...mapState({
      loading: 'loading'
    })
  },

  methods: {
    /**
     * Extract list
     *
     * @param {Array} data - data
     * @param {Object} dragNode - VueComponent
     * @param {Object} node - VueComponent
     * @param {Number} dropPosition - Drop position
     */
    extractList(data, dragNode, node, dropPosition) {
      data.forEach((item, index) => {
        if (item.key === node.eventKey) {
          const list = dropPosition === index ? item.children : data
          this.list = list.filter(entry => entry.key !== dragNode.eventKey)
        }
        this.extractList(item.children, dragNode, node, dropPosition)
      })
    },

    /**
     * Drop action
     *
     * @param {Object} dragNode - VueComponent
     * @param {Array} dragNodesKeys - Drag nodes keys
     * @param {Number} dropPosition - Drop position
     * @param {Boolean} dropToGap - Drop to gap status
     * @param {Object} node - VueComponent
     */
    onDrop({ dragNode, dragNodesKeys, dropPosition, dropToGap, node }) {
      const data = cloneDeep(this.data)
      this.extractList(data, dragNode, node, dropPosition)
      const newNode = {
        ...dragNode.dataRef,
        parent: dropToGap ? node.dataRef.parent : node.eventKey
      }
      let newData = []
      if (!dropToGap) {
        newData = [...this.list, newNode]
      } else {
        this.list.forEach((item, index) => {
          const appendData = [item]
          if (item.key === node.eventKey) {
            if (index >= dropPosition) {
              appendData.unshift(newNode)
            } else {
              appendData.push(newNode)
            }
          }
          newData = [...newData, ...appendData]
        })
      }
      let i = 0
      const batchData = newData.map(item => {
        return {
          id: item.key,
          parent_id: item.parent,
          position: i++
        }
      })
      this.batchUpdate(batchData)
    },

    /**
     * Batch update
     *
     * @param {Array} Item list
     */
    async batchUpdate(list) {
      this.$store.dispatch('setLoading', true)
      try {
        await this.$api.moveMenu({ list })
        this.$fetch()
      } catch (_) {
        this.$notification.error({
          message: this.$t('text.something_wrong')
        })
      } finally {
        this.$store.dispatch('setLoading', false)
      }
    },

    /**
     * Show detail
     *
     * @param {Number} Item Id
     */
    showDetail(id) {
      this.currentId = id
      this.visible = true
    },

    /**
     * Close dialog
     *
     * @param {boolean} canFetch - fetch status
     */
    closeDialog(canFetch) {
      this.visible = false
      if (canFetch) {
        this.$fetch()
      }
    },

    /**
     * Confirm to delete
     *
     * @param {Number} Item Id
     */
    confirmToDelete(id) {
      this.$confirm({
        title: this.$t('text.confirm_to_delete'),
        okText: this.$t('common.yes'),
        okType: 'danger',
        cancelText: this.$t('common.no'),
        onOk: () => this.deleteRecord(id)
      })
    },

    /**
     * Delete record
     *
     * @param {Number} Item Id
     */
    async deleteRecord(id) {
      try {
        this.$store.dispatch('setLoading', true)
        await this.$api.destroyMenu({ id })

        this.$notification.success({
          message: this.$t('text.successfully')
        })
        this.$fetch()
      } catch (_) {
        this.$notification.error({
          message: this.$t('text.something_wrong')
        })
      } finally {
        this.$store.dispatch('setLoading', false)
      }
    }
  }
}
</script>

<style scoped lang="scss">
/deep/ {
  .tree__menu {
    [role="treeitem"] {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      .ant-tree-switcher-icon {
        vertical-align: middle;
        font-size: 20px !important;
      }
      .ant-tree-switcher {
        flex: 0 0 auto;
      }
      .ant-tree-node-content-wrapper {
        flex: 1 1 auto;
        height: auto;
      }
      .ant-tree-child-tree {
        flex: 1 1 100%;
        width: 100%;
      }
      span[draggable="true"] {
        line-height: 24px;
      }
      .ant-tree-title {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        .menu-tree__title {
          flex: 1 1 auto;
        }
        .menu-tree__action {
          flex: 0 0 auto;
          width: 66px;
        }
      }
    }
  }
}
</style>
