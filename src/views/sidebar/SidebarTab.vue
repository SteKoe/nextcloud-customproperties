<template>
	<Tab :id="id"
		:icon="icon"
		:name="name">
		<TabContent :file-info="fileInfo" />
	</Tab>
</template>

<script>
import Tab from '@nextcloud/vue/dist/Components/AppSidebarTab'
import TabContent from './TabContent'

export default {
	name: 'SidebarTab',
	components: {
		TabContent,
		Tab,
	},
	props: {
		fileInfo: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			name: t('customproperties', 'Properties'),
			icon: 'icon-info',
			loading: true,
		}
	},
	computed: {
		id() {
			return 'customproperties'
		},
		activeTab() {
			return this.$parent.activeTab
		},
	},
}
</script>
