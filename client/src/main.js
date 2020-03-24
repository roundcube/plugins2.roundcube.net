import '@babel/polyfill'
import '@fortawesome/fontawesome-free/css/all.css'
import 'mutationobserver-shim'
import './plugins/bootstrap-vue'
import './plugins/vue-moment'
import Vue from 'vue'
import App from './app.vue'
import router from './router'

Vue.config.productionTip = false

new Vue({
  router,
  render: h => h(App)
}).$mount('body')
