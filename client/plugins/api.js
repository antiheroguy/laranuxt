import { camelCase } from 'lodash'
import { REFRESH_TOKEN } from '~/constants/cookies'
import routes from '~/configs/routes'

export default ({ $axios, $cookies, app }, inject) => {
  class API {
    constructor() {
      this.checkRefreshToken({ $axios, $cookies, app })

      this.axios = $axios

      this.generateMethods()
    }

    /**
     * Check refresh token
     *
     * @param {Object} context
     */
    checkRefreshToken({ $axios, $cookies, app }) {
      $axios.onError(async error => {
        const statusCode = error.response.status

        // Refresh token if expired
        const refreshToken = $cookies.get(REFRESH_TOKEN)
        if (statusCode === 401) {
          if (refreshToken) {
            try {
              const { data } = await $axios.post('/refresh', { refresh_token: refreshToken })
              $cookies.set(REFRESH_TOKEN, data.refresh_token, { maxAge: process.env.LONG_LIVED_TOKEN_LIFETIME, path: '/' })
              if (app.$auth) {
                app.$auth.setUserToken(data.access_token)
              }
              const originalRequest = error.config
              originalRequest.headers.Authorization = 'Bearer ' + data.access_token
              return $axios(originalRequest)
            } catch (e) {
              $cookies.remove(REFRESH_TOKEN, { path: '/' })
            }
          }

          if (app.$auth && app.$auth.loggedIn) {
            app.$auth.logout()
          }
        }
      })
    }

    /**
     * Generate methods for plugin
     */
    generateMethods() {
      Object.entries(routes).forEach(([path, methods]) => {
        // Generate method for resource
        if (methods.resource) {
          const resource = {
            index: { method: 'get', path },
            store: { method: 'post', path },
            show: { method: 'get', path: `${path}/{id}` },
            update: { method: 'put', path: `${path}/{id}` },
            destroy: { method: 'delete', path: `${path}/{id}` }
          }

          Object.entries(resource).forEach(([action, config]) => {
            this.buildMethod(config.path, config.method, {
              ...methods.resource,
              name: camelCase(`${action}-${path}`)
            })
          })
        }

        Object.entries(methods).forEach(([method, options]) => {
          this.buildMethod(path, method, options)
        })
      })
    }

    /**
     * Method builder
     *
     * @param {string} path
     * @param {string} method
     * @param {object} options
     *
     * @return {object}
     */
    buildMethod(path, method, options = {}) {
      if (!['get', 'post', 'put', 'patch', 'delete', 'head', 'options'].includes(method)) {
        return
      }

      const key = options.name || camelCase(`${method}-${path}`)
      this[key] = (...args) => {
        const matches = path.match(/(?<=\{)(.*?)(?=\})/gm)

        // Detect dynamic routes
        if (matches) {
          matches.forEach(param => {
            const hasParam = args.find(arg => {
              if (arg[param]) {
                path = path.replace(`{${param}}`, arg[param])
              }
              return arg[param]
            })

            if (!hasParam) {
              throw new Error(`Missing required parameter "${param}" for method "${key}"`)
            }
          })
        }

        if (options.file && ['post', 'put', 'patch'].includes(method)) {
          method = 'post'
        }

        return this.axios[method](path, ...args)
      }
    }
  }

  inject('api', new API())
}
