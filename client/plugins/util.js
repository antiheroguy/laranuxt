export default (context, inject) => {
  const util = {
    /**
     * Get form data
     *
     * @param {Object} payload
     * @param {Array} required
     * @param {Boolean} file
     * @return {Object} form data
     */
    getFormData: ({ payload, required, file }) => {
      const formObj = {}
      const formData = new FormData()
      Object.entries(payload).forEach(([key, value]) => {
        if (required.includes(key) && [undefined, null, ''].includes(value)) {
          throw new Error(`Property "${key}" is required`)
        }

        formObj[key] = value
        if (file) {
          formData.append(key, value)
        }
      })

      return file ? formData : formObj
    }
  }

  inject('util', util)
}
