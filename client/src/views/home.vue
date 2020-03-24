<template>
  <main class="flex-shrink-0">
    <PluginSearch/>
    <div class="container pb-5">
      <GettingStarted/>
    </div>
  </main>
</template>

<script>
// @ is an alias to /src
import GettingStarted from '@/components/gettingstarted.vue'
import PluginSearch from '@/components/pluginsearch.vue'

export default {
  name: 'home',
  components: {
    GettingStarted,
    PluginSearch
  }
}
</script>
