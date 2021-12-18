import { mapState } from 'vuex'

export default {
  async fetch() {
    this.$store.dispatch('setLoading', true)

    try {
      const params = this.$route.query
      if (this.defaultParams && typeof this.defaultParams === 'object') {
        Object.entries(this.defaultParams).forEach(([key, value]) => {
          params[key] = value
        })
      }

      const { data: { data, meta } } = await this.repository.list({ params })
      this.pagination = {
        ...this.pagination,
        total: meta ? meta.total : data.length,
        current: params.page ? +params.page : 1,
        pageSize: params.limit ? +params.limit : 20
      }
      this.data = data
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
      data: [],
      pagination: {
        showSizeChanger: true,
        showTotal: (total, range) => this.$t('pagination.total', { total }),
        buildOptionText: ({ value }) => this.$createElement('span', [value])
      }
    }
  },

  computed: {
    ...mapState({
      loading: 'loading'
    }),

    header() {
      return []
    },

    columns() {
      return this.header.map(item => {
        item.sortOrder = null
        if (this.$route.query.order_by === item.dataIndex && this.$route.query.order_type) {
          item.sortOrder = +this.$route.query.order_type === 1 ? 'ascend' : 'descend'
        }
        return item
      })
    },

    repository() {
      return this.$api[this.resource]
    }
  },

  watch: {
    /**
     * Watching changes of route
     */
    '$route.query': '$fetch'
  },

  methods: {
    /**
     * Handle table change
     *
     * @param {Object} Pagination
     * @param {Object} Filters
     * @param {Object} Sorter
     */
    handleTableChange(pagination, filters, sorter) {
      this.replaceQuery({
        limit: pagination.pageSize,
        page: pagination.current,
        order_by: sorter.order ? sorter.field : null,
        order_type: sorter.order ? +(sorter.order === 'ascend') : null
      })
    },

    /**
     * Replace query
     *
     * @param {Object} Data query
     */
    replaceQuery(data) {
      const query = {}
      const newQuery = {
        ...this.$route.query,
        ...data
      }
      Object.entries(newQuery).forEach(([key, value]) => {
        if (value !== '' && value !== null && value !== undefined) {
          query[key] = value
        }
      })
      if (JSON.stringify(query) === JSON.stringify(this.$route.query)) {
        this.$fetch()
      } else {
        this.$router.push({ query })
      }
    },

    /**
     * Go to new
     */
    gotoNew() {
      this.$router.push(`/${this.resource}/new`)
    },

    /**
     * Go to detail
     *
     * @param {Number} id
     */
    gotoDetail(id) {
      this.$router.push(`/${this.resource}/${id}`)
    },

    /**
     * Confirm to delete
     *
     * @param {Number} id
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
     * @param {Number} id
     */
    async deleteRecord(id) {
      this.$store.dispatch('setLoading', true)

      try {
        await this.repository.delete(id)

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
