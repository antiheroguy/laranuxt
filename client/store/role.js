import { SET_MODEL, SET_LIST } from '~/constants/mutation-types'

export const state = () => ({
  model: {},
  list: []
})

export const mutations = {
  [SET_LIST]: (state, payload) => {
    state.list = payload
  },
  [SET_MODEL]: (state, payload) => {
    state.model = payload
  }
}

export const actions = {
  /**
   * Get list role
   *
   * @param {Function} commit
   * @param {Array} payload
   * @return {Array} role list
   */
  async getList({ commit }, payload) {
    payload.params.not_admin = true
    const { data } = await this.$api.indexRole(payload)
    commit(SET_LIST, data.data)
    return data
  },

  /**
   * Get role detail
   *
   * @param {Function} commit
   * @param {Object} payload
   * @return {Object} role detail
   */
  async getModel({ commit }, { id }) {
    let model = {
      permissionIds: []
    }
    if (id) {
      const { data: { data } } = await this.$api.showRole({ id })
      model = data
      model.permissionIds = model.permissions.map(item => item.id)
    }
    commit(SET_MODEL, model)
    return model
  },

  /**
   * Create/Update role
   *
   * @param {Function} commit
   * @param {Object} payload
   * @return {Object} role detail
   */
  async saveModel({ commit }, payload) {
    const form = this.$util.getFormData({ payload, required: ['id', 'name'] })
    form.permissions = payload.permissionIds

    const { data: { data: { model } } } = payload.id ? await this.$api.updateRole(form) : await this.$api.storeRole(form)
    commit(SET_MODEL, model)
    return model
  }
}
