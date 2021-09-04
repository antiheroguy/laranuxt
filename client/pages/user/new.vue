<template>
  <a-card class="mb-4">
    <template slot="title">
      {{ $t('common.create') }} {{ $t('module.user') }}
    </template>

    <user-form
      @save="goBack"
      @cancel="goBack"
    />
  </a-card>
</template>
<script>

import UserForm from '~/components/templates/UserForm'

export default {
  components: {
    UserForm
  },

  methods: {
    /**
     * Go to list
     */
    goBack() {
      this.$router.push({
        path: '../',
        append: true
      })
    }
  }
}
</script>
