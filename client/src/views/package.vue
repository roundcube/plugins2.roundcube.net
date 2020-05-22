<template>
  <main class="flex-shrink-0">
    <PluginSearch/>
    <div class="container pb-8" v-if="loading">
      <div class="spinner">
        <div class="double-bounce1"></div>
        <div class="double-bounce2"></div>
      </div>
    </div>
    <div class="container py-3" v-if="!loading && isError">
      <h1 class="title error"><i class="far fa-frown"></i> {{ errorMessage }}</h1>
      <p>Type the name of the requested package into the search form above.</p>
    </div>
    <div class="container pb-5" v-if="!loading && !isError">
      <div class="row pt-3 package">
        <div class="col-sm-12 package-header">
          <h1 class="title">
            <a :href="`https://packagist.org/packages/${vendor}/${name}`" title="Open on packagist.org" v-if="!pkg.abandoned">{{ pkg.name }}</a>
            <span v-if="pkg.abandoned">{{ pkg.name }}</span>
          </h1>
        </div>
      </div>
      <div class="d-flex row package">
        <div class="col-md-8">
          <p class="requireme">
            <i class="fas fa-download"></i>
            <input type="text" readonly="readonly" :value="`composer require ${vendor}/${name}`">
          </p>
          <p class="description">{{ pkg.description }}</p>

          <p class="buttons" v-if="!pkg.abandoned">
            <b-button :href="`https://packagist.org/packages/${vendor}/${name}`" variant="primary" size="md">
              <i class="fas fa-external-link-alt"></i>
              Open on packagist.org
            </b-button>
          </p>

          <p class="bg-warning p-3 mb-5" v-if="pkg.abandoned">
            <strong>This plugin is abandoned and does not get any updates!</strong><br>
            If you're the author of this plugin, please read the instructions <a href="#/about/" class="text-secondary">how to submit plugins</a>
            to packagist.org.
          </p>

          <h3>Versions</h3>
          <ul class="versions">
            <li v-for="item in versions" v-bind:key="item.version" class="version">
              <a class="title pointer" :class="{ selected: item.version == versionKey }" @click="selectVersion(item.version)">
                <span class="version-number">{{ item.version }}</span>
                <span class="release-date">{{ item.time | moment('YYYY-MM-DD hh:mm') }} UTC</span>
              </a>
            </li>
          </ul>

        </div>

        <div class="ml-auto col-md-4">
          <div class="row package-aside">
            <div class="details col-xs-12 col-sm-6 col-md-12">
              <h5>Maintainers</h5>
              <p class="maintainers">
                <a v-for="item in pkg.maintainers" v-bind:key="item.name" :href="`https://packagist.org/users/${item.name}`">
                  <img :title="item.name" :src="item.avatar_url" width="48" height="48">
                </a>
              </p>

              <h5>Details</h5>
              <p class="canonical"><a :href="pkg.repository" title="Canonical Repository URL">{{ pkg.repository }}</a></p>
              <p><a rel="nofollow noopener external noindex" v-if="version.homepage" :href="version.homepage">Homepage</a></p>
              <p><a rel="nofollow noopener external noindex" v-if="version.source" :href="version.source.url">Source</a></p>
              <!-- <p><a rel="nofollow noopener external noindex" href="https://github.com/blind-coder/rcmcarddav/issues">Issues</a></p> -->
            </div>

            <div class="facts col-xs-12 col-sm-6 col-md-12">
              <p><span>Installs</span> {{ pkg.downloads.total }} </p>
              <p><span>Dependents</span> {{ pkg.dependents }}</p>
              <p><span>Suggesters</span> {{ pkg.suggesters }}</p>
              <p v-if="pkg.github_stars"><span>Stars</span> {{ pkg.github_stars }}</p>
              <p v-if="pkg.github_watchers"><span>Watchers</span> {{ pkg.github_watchers }}</p>
              <p v-if="pkg.github_forks"><span>Forks</span> {{ pkg.github_forks }}</p>
              <p v-if="pkg.github_open_issues"><span>Open Issues</span> {{ pkg.github_open_issues }}</p>
              <!-- <p><span>Type:</span> {{ pkg.type }}</p> -->
            </div>
          </div>
        </div>
      </div><!-- .row -->
    </div>
  </main>
</template>

<script>
import PluginSearch from '@/components/pluginsearch.vue'
import moment from 'moment'
import _ from 'lodash'

const API_URL = 'https://packagist.org/packages/:vendor/:name.json'
const OLD_URL = 'https://plugins.roundcube.net/d/packages/:vendor/:name.json'

export default {
  name: 'Package',
  components: {
    PluginSearch
  },

  data () {
    return {
      name: '',
      vendor: '',
      loading: true,
      isError: false,
      errorMessage: null,
      pkg: { downloads: { total: 0 } },
      versions: [],
      version: {},
      versionKey: null
    }
  },

  methods: {
    async fetch () {
      this.loading = true
      let res = await fetch(API_URL.replace(':vendor', this.vendor).replace(':name', this.name))
      let data = await res.json()
      let abandoned = false

      // retry with OLD_URL
      if (!res.ok) {
        res = await fetch(OLD_URL.replace(':vendor', this.vendor).replace(':name', this.name))
        data = await res.json()
        abandoned = true
      }

      // handle error response
      if (!res.ok) {
        this.isError = true
        this.errorMessage = data.message || '404 Not Found'
        this.loading = false
        return
      }

      this.pkg = data.package || {}
      this.pkg.abandoned = abandoned

      const normalizedVersion = function (item) {
        let v = item.version_normalized

        if (item.version === 'dev-master') {
          v = '9999999-dev'
        } else if (item.version.indexOf('dev') === 0) {
          v = '0000-' + item.version
        }

        return v
      }

      this.versions = _.map(_.values(data.package.versions || {}), (item) => {
        item.time = moment.parseZone(item.time).utc()
        return item
      })
      this.versions.sort((a, b) => normalizedVersion(a) > normalizedVersion(b) ? -1 : 1)

      // console.log(data, this.versions)
      let versionKey = this.versions[0].version
      if (data.package.versions['dev-master']) {
        versionKey = 'dev-master'
      }
      this.selectVersion(versionKey)
      this.loading = false
    },

    selectVersion: function (key) {
      this.versionKey = key
      this.version = this.pkg.versions[key]
    }
  },

  mounted () {
    this.vendor = this.$route.params.vendor
    this.name = this.$route.params.name
    // this.fetch()
  },

  watch: {
    '$route.params': {
      handler (newValue) {
        this.vendor = this.$route.params.vendor
        this.name = this.$route.params.name
        this.fetch()
      },
      immediate: true
    }
  }
}
</script>

<style lang="less" scoped>
.package-header {
  h1.title {
    margin: -5px 0 15px;
    margin-bottom: 15px;
    padding: 0 0 15px;
    border-bottom: 1px solid #f28d1a;

    a {
      color: #2d2d32;
    }
  }
}

.package {
  .requireme {
    input {
      cursor: text;
      outline: none;
      border: 0 !important;
      border-radius: 0;
      background-color: transparent;
      font-family: Consolas, "Courier New", Courier, monospace;
      min-width: 80%;
      position: relative;
      top: -3px;
      left: 2px;
    }
  }

  h3 {
    font-size: 18px;
  }

  p {
    font-size: 14px;
  }

  p.description {
    margin-top: 0;
    font-size: 20px;
    font-style: italic;
    font-weight: 300;
    margin-bottom: 20px;
  }

  p.buttons {
    margin: 40px 0;
  }

  .details p,
  .facts p {
    margin: 0;
  }

  p.maintainers {
    margin-bottom: 15px;
  }

  .package-aside {
    background: #fbfbfb;
    border-radius: 2px;
    border-top: 0;
    padding: 10px 0;
    margin-left: -15px;
    margin-right: -15px;
  }

  .details .canonical {
    text-overflow: ellipsis;
    overflow: hidden;
    width: 100%;
    white-space: nowrap;
  }

  .facts {
    margin-top: 15px;

    span {
      display: inline-block;
      width: 100px;
    }
  }

  .versions {
    margin: 10px 0;
    padding: 0;
    list-style: none;

    li.version {
      a.title {
        display: block;
        padding: 8px 0;
        border-top: 1px solid transparent;
        border-bottom: 1px solid transparent;
        cursor: pointer;

        .version-number {
          float: left;
        }

        .release-date {
          float: right;
          font-size: .7em;
          line-height: 2em;
        }

        &.selected {
          border-top: 1px solid #f28d1a;
          border-bottom: 1px solid #f28d1a;
        }

        &:after {
          content: "";
          display: table;
          clear: both;
        }
      }
    }
  }
}

i.fas,
i.far {
  margin-right: 6px;
}

.spinner {
  width: 60px;
  height: 60px;

  position: relative;
  margin: 100px auto;
}

.double-bounce1,
.double-bounce2 {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: #37BEFF;
  opacity: 0.5;
  position: absolute;
  top: 0;
  left: 0;
  -webkit-animation: sk-bounce 2.0s infinite ease-in-out;
  animation: sk-bounce 2.0s infinite ease-in-out;
}

.double-bounce2 {
  -webkit-animation-delay: -1.0s;
  animation-delay: -1.0s;
}

@-webkit-keyframes sk-bounce {
  0%, 100% { -webkit-transform: scale(0.0) }
  50% { -webkit-transform: scale(1.0) }
}

@keyframes sk-bounce {
  0%, 100% {
    transform: scale(0.0);
    -webkit-transform: scale(0.0);
  } 50% {
    transform: scale(1.0);
    -webkit-transform: scale(1.0);
  }
}
</style>
