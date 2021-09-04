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
   * Get list menu
   *
   * @param {Function} commit
   * @param {Array} payload
   * @return {Array} menu list
   */
  async getList({ commit }, payload) {
    const { data } = await this.$api.indexMenu(payload)
    commit(SET_LIST, data.data)
    return data
  },

  /**
   * Get menu detail
   *
   * @param {Function} commit
   * @param {Object} payload
   * @return {Object} menu detail
   */
  async getModel({ commit }, { id }) {
    let model = {
      roleIds: []
    }
    if (id) {
      const { data: { data } } = await this.$api.showMenu({ id })
      model = data
      model.roleIds = model.roles.map(item => item.id)
    }
    commit(SET_MODEL, model)
    return model
  },

  /**
   * Create/Update menu
   *
   * @param {Function} commit
   * @param {Object} payload
   * @return {Object} menu detail
   */
  async saveModel({ commit }, payload) {
    const form = this.$util.getFormData({ payload, required: ['id', 'title'] })
    form.roles = payload.roleIds

    const { data: { data: { model } } } = payload.id ? await this.$api.updateMenu(form) : await this.$api.storeMenu(form)
    commit(SET_MODEL, model)
    return model
  }
}
