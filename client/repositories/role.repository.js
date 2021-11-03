export default $axios => ({
  resource: 'role',

  /**
   * Get list permissions
   *
   * @returns {Object}
   */
  getPermission() {
    return $axios.get('/permission')
  }
})
