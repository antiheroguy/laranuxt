import Vue from 'vue'

Vue.directive('auto-focus', {
  inserted: el => el.focus()
})

Vue.directive('click-away', {
  bind(el, { value }) {
    if (typeof value !== 'function') {
      return
    }

    document.addEventListener('click', e => el.contains(e.target) || value())
  }
})

Vue.directive('hold', {
  bind(el, { value }) {
    if (typeof value !== 'function') {
      return
    }

    let pressTimer = null

    const start = e => {
      if (e.type === 'click' && e.button !== 0) {
        return
      }

      if (pressTimer === null) {
        pressTimer = setTimeout(() => value(e), 1000)
      }
    }

    const cancel = () => {
      if (pressTimer !== null) {
        clearTimeout(pressTimer)
        pressTimer = null
      }
    }

    const startActions = ['mousedown', 'touchstart']
    const endActions = ['click', 'mouseout', 'touchend', 'touchcancel']

    startActions.forEach(e => el.addEventListener(e, start))
    endActions.forEach(e => el.addEventListener(e, cancel))
  }
})
