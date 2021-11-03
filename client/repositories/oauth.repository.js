export default $axios => ({
  resource: 'oauth',

  /**
   * Get redirect uri
   *
   * @returns {Object}
   */
  getRedirectURI(config = {}) {
    return $axios.get('/redirect-uri', config)
  },

  /**
   * Handle oauth callback
   *
   * @returns {Object}
   */
  handleCallback(data, config = {}) {
    return $axios.post('/handle-callback', data, config)
  }
})
