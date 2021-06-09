import Vue from 'vue'
import { translate, translatePlural } from '@nextcloud/l10n'

import SidebarTab from './views/sidebar/SidebarTab'
import TabContent from './views/sidebar/TabContent'

window.addEventListener('DOMContentLoaded', () => {
	if (OCA.Files && OCA.Files.Sidebar) {
		let tab

		try {
			// Nextcloud 20
			tab = new OCA.Files.Sidebar.Tab('customproperties', SidebarTab)
		} catch (error) {
			// Nextcloud > 21
			Vue.prototype.t = translate
			Vue.prototype.n = translatePlural

			// Init Sharing tab component
			const View = Vue.extend(TabContent)
			let TabInstance = null

			tab = new OCA.Files.Sidebar.Tab({
				id: 'customproperties',
				name: t('customproperties', 'Properties'),
				icon: 'icon-info',

				async mount(el, fileInfo, context) {
					if (TabInstance) {
						TabInstance.$destroy()
					}
					TabInstance = new View({
						parent: context,
						data() {
							return {
								fileInfo_: fileInfo,
							}
						},
					})
					await TabInstance.updateFileInfo(fileInfo)
					TabInstance.$mount(el)
				},
				async update(fileInfo) {
					await TabInstance.updateFileInfo(fileInfo)
				},
				destroy() {
					TabInstance.$destroy()
					TabInstance = null
				},
			})
		}

		OCA.Files.Sidebar.registerTab(tab)
	}
})
