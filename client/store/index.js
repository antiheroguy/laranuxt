import { SET_LOADING, SET_IS_PC, SET_IS_SIDEBAR_COLLAPSED } from '~/constants/mutation-types'

export const state = () => ({
  loading: false,
  isPC: true,
  isSidebarCollapsed: false
})

export const getters = {
  loading: state => state.loading,
  isPC: state => state.isPC,
  isSidebarCollapsed: state => state.isSidebarCollapsed
}

export const mutations = {
  [SET_LOADING]: (state, payload) => {
    state.loading = payload
  },
  [SET_IS_PC]: (state, payload) => {
    state.isPC = payload
  },
  [SET_IS_SIDEBAR_COLLAPSED]: (state, payload) => {
    state.isSidebarCollapsed = payload
  }
}

export const actions = {
  /**
   * Set loading global
   *
   * @param {Function} commit
   * @param {Boolean} payload
   */
  setLoading({ commit }, payload) {
    commit(SET_LOADING, payload)
  }
}
