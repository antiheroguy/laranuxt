<template>
  <a-card class="mb-4">
    <template slot="title">
      {{ $t('common.edit') }} {{ $t('module.role') }}
    </template>

    <role-form
      :id="id"
      @save="goBack"
      @cancel="goBack"
    />
  </a-card>
</template>

<script>
import RoleForm from '~/components/templates/RoleForm'

export default {
  components: {
    RoleForm
  },

  data() {
    return {
      id: +this.$route.params.id || 0
    }
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
