import Vue from 'vue'
import { translate, translatePlural } from '@nextcloud/l10n'

import AdminSettings from './admin/AdminSettings.vue'

Vue.prototype.t = translate
Vue.prototype.n = translatePlural

// eslint-disable-next-line no-new
export default new Vue({
	el: '#customproperties_settings',
	render: h => h(AdminSettings),
})
