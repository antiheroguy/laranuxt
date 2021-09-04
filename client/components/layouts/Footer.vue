<template>
  <a-layout-footer class="layout__footer">
    <a-row
      type="flex"
      :gutter="30"
    >
      <a-col
        :span="24"
        :sm="12"
      >
        © 2020
        <a
          href="https://www.hero.tk/"
          target="_blank"
        >Hero - Adminstrator</a>
      </a-col>

      <a-col
        :span="24"
        :sm="12"
        class="col__footer--right"
      >
        <span>Powered by</span> <a
          href="https://www.hero.tk/"
          target="_blank"
        >Hero</a>
      </a-col>
    </a-row>
  </a-layout-footer>
</template>

<style scoped lang="scss">
.layout__footer {
  border-top: 1px solid #d8dbe0;
  @include mq(max_xs) {
    text-align: center;
  }
  .col__footer--right {
    text-align: right;
    @include mq(max_xs) {
      text-align: center;
    }
  }
}
</style>
