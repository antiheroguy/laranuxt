module.exports = {
  root: true,
  env: {
    browser: true,
    node: true
  },
  parserOptions: {
    parser: 'babel-eslint'
  },
  extends: [
    '@nuxtjs',
    'plugin:vue/essential',
    'plugin:vue/strongly-recommended',
    'plugin:nuxt/recommended'
  ],
  // add your custom rules here
  rules: {
    'no-console': 0,
    'space-before-function-paren': ['error', {
      anonymous: 'never',
      named: 'never',
      asyncArrow: 'always'
    }],
    'arrow-parens': ['error', 'as-needed'],
    'vue/html-self-closing': ['error', {
      html: {
        void: 'always',
        normal: 'always',
        component: 'always'
      },
      svg: 'always',
      math: 'always'
    }],
    'vue/order-in-components': ['error',
      {
        order: [
          'layout',
          'components',
          'mixins',
          'middleware',
          'asyncData',
          'fetch',
          'serverPrefetch',
          'data',
          'computed',
          'watch',
          'created',
          'mounted',
          'updated',
          'destroyed',
          'methods'
        ]
      }
    ]
  }
}
