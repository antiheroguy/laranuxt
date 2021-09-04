export default {
  me: {
    post: {
      name: 'updateProfile'
    }
  },
  user: {
    resource: {}
  },
  role: {
    resource: {}
  },
  permission: {
    get: {}
  },
  menu: {
    resource: {}
  },
  'menu/move': {
    post: {
      name: 'moveMenu'
    }
  },
  'redirect-uri': {
    get: {
      name: 'getRedirectURI'
    }
  },
  'handle-callback': {
    post: {
      name: 'handleCallback'
    }
  }
}
