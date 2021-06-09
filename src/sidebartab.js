import Sidebar from './views/sidebar/Sidebar.vue'

window.addEventListener('DOMContentLoaded', () => {
	if (OCA.Files && OCA.Files.Sidebar) {
		OCA.Files.Sidebar.registerTab(new OCA.Files.Sidebar.Tab('customproperties', Sidebar))
	}
})
