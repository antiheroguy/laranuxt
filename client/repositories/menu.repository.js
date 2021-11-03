export default $axios => ({
  resource: 'menu',

  /**
   * Move menu
   *
   * @returns {Object}
   */
  move(data, config = {}) {
    return $axios.post('/menu/move', data, config)
  }
})
