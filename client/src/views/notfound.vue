<template>
  <main class="flex-shrink-0">
    <PluginSearch/>
    <div class="container py-3">
      <h1 class="title error"><i class="far fa-frown"></i> Page Not Found</h1>
      <p>Type the name of the requested package into the search form above
        or start over from the <router-link to="/">home page</router-link>.
      </p>
    </div>
  </main>
</template>

<script>
import PluginSearch from '@/components/pluginsearch.vue'

export default {
  name: 'NotFound',
  components: {
    PluginSearch
  }
}

</script>

<style lang="less" scoped>
</style>
