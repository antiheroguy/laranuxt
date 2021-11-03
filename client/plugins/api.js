import { REFRESH_TOKEN } from '~/constants/cookies'
import CreateRepository from '~/repositories/service.container'

export default ({ $axios, $cookies, app }, inject) => {
  $axios.onError(async error => {
    if (error.response && error.response.status === 401) {
      // Refresh token if expired
      const refreshToken = $cookies.get(REFRESH_TOKEN)
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

      app.$auth.reset()
    }

    return Promise.reject(error)
  })

  inject('api', CreateRepository($axios))
}
