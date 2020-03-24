import Vue from 'vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowAltCircleDown, faStar, faDownload, faExternalLinkAlt, faFrown } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faArrowAltCircleDown, faStar, faDownload, faExternalLinkAlt, faFrown)
Vue.component('font-awesome-icon', FontAwesomeIcon)
