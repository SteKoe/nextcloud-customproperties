import Vue from 'vue'
import AdminSettings from './AdminSettings.vue'

Vue.prototype.t = t
Vue.prototype.n = n
Vue.prototype.OC = OC
Vue.prototype.OCA = OCA

window.addEventListener('DOMContentLoaded', () => {
	new Vue({
		el: "#customproperties_settings",
		render: h => h(AdminSettings)
	})
})