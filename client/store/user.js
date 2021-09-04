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
   * Get list user
   *
   * @param {Function} commit
   * @param {Array} payload
   * @return {Array} user list
   */
  async getList({ commit }, payload) {
    const { data } = await this.$api.indexUser(payload)
    commit(SET_LIST, data.data)
    return data
  },

  /**
   * Get user detail
   *
   * @param {Function} commit
   * @param {Object} payload
   * @return {Object} user detail
   */
  async getModel({ commit }, { id }) {
    let model = {
      roleIds: []
    }
    if (id) {
      const { data: { data } } = await this.$api.showUser({ id })
      model = data
      model.roleIds = model.roles.map(item => item.id)
    }
    commit(SET_MODEL, model)
    return model
  },

  /**
   * Create/Update user
   *
   * @param {Function} commit
   * @param {Object} payload
   * @return {Object} user detail
   */
  async saveModel({ commit }, payload) {
    const form = this.$util.getFormData({ payload, required: ['id', 'name', 'email', 'password'] })
    form.roles = payload.roleIds

    const { data: { data: model } } = payload.id ? await this.$api.updateUser(form) : await this.$api.storeUser(form)
    commit(SET_MODEL, model)
    return model
  }
}
