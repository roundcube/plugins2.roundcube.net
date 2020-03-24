<template>
  <div class="banner search py-3">
    <div class="container">
      <vue-bootstrap-typeahead
        :data="results"
        v-model="pluginSearch"
        size="lg"
        placeholder="Search Plugins..."
        backgroundVariant="bg-light"
        :serializer="s => s.name"
        @hit="selectItem"
      >
      <template slot="suggestion" slot-scope="{ data, htmlText }">
        <div class="row">
          <div class="col-sm-9 col-lg-10">
            <h4 v-html="htmlText"></h4>
            <div>{{ data.description }}</div>
          </div>
          <div class="col-sm-3 col-lg-2">
            <p class="metadata">
              <span class="metadata-block"><i class="fas fa-arrow-alt-circle-down"></i> {{ data.downloads }}</span>
              <span class="metadata-block"><i class="fas fa-star"></i> {{ data.favers }}</span>
            </p>
          </div>
        </div>
      </template>
      </vue-bootstrap-typeahead>
    </div>
  </div>
</template>

<script>
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
import _ from 'lodash'

const API_URL = 'https://packagist.org/search.json?q=:query&type=roundcube-plugin'

export default {
  name: 'PluginSearch',
  components: {
    VueBootstrapTypeahead
  },

  data () {
    return {
      results: [],
      pluginSearch: '',
      selectedItem: {}
    }
  },

  methods: {
    async getResults (query) {
      const res = await fetch(API_URL.replace(':query', query))
      const data = await res.json()
      this.results = data.results
    },

    selectItem (item) {
      this.selectedItem = item
      this.openDetails()
    },

    openDetails () {
      const [vendor, name] = this.selectedItem.name.split('/')
      this.$router.push({ name: 'package', params: { vendor, name } })
    }
  },

  watch: {
    pluginSearch: _.debounce(function (query) { this.getResults(query) }, 200)
  }
}
</script>

<style scoped lang="less">

.banner {
  background-color: #c6e6f4;
}

.metadata-block {
  display: block;
  margin: 5px 0;

  i.fas {
    margin-right: .6em;
  }
}

.list-group.vbt-autcomplete-list {
  max-height: calc(~"100% - 150px");
}

</style>
