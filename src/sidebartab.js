import Vue from 'vue'
import { translate, translatePlural } from '@nextcloud/l10n'

import Sidebar from './views/sidebar/Sidebar.vue'

Vue.prototype.t = translate
Vue.prototype.n = translatePlural

// Init Sharing tab component
const View = Vue.extend(Sidebar)
let TabInstance = null

window.addEventListener('DOMContentLoaded', () => {
	if (OCA.Files && OCA.Files.Sidebar) {
		OCA.Files.Sidebar.registerTab(new OCA.Files.Sidebar.Tab({
			id: 'customproperties',
			name: t('customproperties', 'Properties'),
			icon: 'icon-info',

			async mount(el, fileInfo, context) {
				if (TabInstance) {
					TabInstance.$destroy()
				}
				TabInstance = new View({
					parent: context,
				})
				await TabInstance.update(fileInfo)
				TabInstance.$mount(el)
			},
			update(fileInfo) {
				TabInstance.update(fileInfo)
			},
			destroy() {
				TabInstance.$destroy()
				TabInstance = null
			},
		}))
	}
})
