import MenuRepository from './menu.repository'
import OauthRepository from './oauth.repository'
import RoleRepository from './role.repository'

/**
 * Make repository
 *
 * @param {Object} $axios
 * @param {String} resource
 * @returns {Object}
 */
const make = ($axios, resource) => ({
  resource,

  /**
   * Get list resources
   *
   * @param {Object} config
   * @returns {Object}
   */
  list(config = {}) {
    return $axios.get(`${this.resource}`, config)
  },

  /**
   * Create resource
   *
   * @param {Object} data
   * @param {Object} config
   * @returns {Object}
   */
  create(data, config = {}) {
    return $axios.post(`${this.resource}`, data, config)
  },

  /**
   * Show detail resource
   *
   * @param {Number} id
   * @param {Object} config
   * @returns {Object}
   */
  show(id, config = {}) {
    return $axios.get(`${this.resource}/${id}`, config)
  },

  /**
   * Update resource
   *
   * @param {Number} id
   * @param {Object} data
   * @param {Object} config
   * @returns {Object}
   */
  update(id, data, config = {}) {
    return $axios.put(`${this.resource}/${id}`, data, config)
  },

  /**
   * Delete resource
   *
   * @param {Number} id
   * @param {Object} config
   * @returns {Object}
   */
  delete(id, config = {}) {
    return $axios.delete(`${this.resource}/${id}`, config)
  }
})

/**
 * Binding repository
 *
 * @param {Object} $axios
 * @param {Functiion} createRepository
 * @returns {Object}
 */
const bind = ($axios, createRepository) => {
  const repository = createRepository($axios)
  if (!repository.resource) {
    throw new Error('Missing resource')
  }

  return {
    ...make($axios, repository.resource),
    ...repository
  }
}

export default $axios => ({
  menu: bind($axios, MenuRepository),
  oauth: bind($axios, OauthRepository),
  role: bind($axios, RoleRepository),
  user: make($axios, 'user')
})
