<template>
	<section class="section">
		<h2>{{ t('customproperties', 'Custom Properties') }}</h2>
		<p class="settings-hint">
			{{
				t('customproperties', 'Custom properties defined here are available to all users. They are shown in "Properties" tab in sidebar view. They can be accessed via WebDAV.')
			}}
		</p>
		<h3>{{ t('customproperties', 'Manage properties') }}</h3>
		<p class="settings-hint">
			{{
				t('customproperties', 'Deleting properties will not wipe already set property values.')
			}}
		</p>
		<div class="form-group">
			<CreateCustomPropertyForm @createProperty="createProperty" />
			<hr>
			<CustomPropertyList :properties="properties" @update="updateProperty" @remove="removeProperty" />
		</div>
	</section>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'
import '@nextcloud/dialogs/styles/toast.scss'
import CustomPropertyList from './CustomPropertyList'
import CreateCustomPropertyForm from './CreateCustomPropertyForm'

export default {
	name: 'AdminSettings',
	components: {
		CreateCustomPropertyForm,
		CustomPropertyList,
	},
	data() {
		return {
			icon: 'icon-info',
			loading: true,
			name: t('properties', 'Properties'),
			properties: [],
			property: {
				propertylabel: null,
			},
		}
	},
	computed: {
		id() {
			return this.name.toLowerCase().replace(/ /g, '-')
		},
		activeTab() {
			return this.$parent.activeTab
		},
	},
	created() {
		this.getDataFromApi()
	},
	methods: {
		async getDataFromApi() {
			const url = generateUrl('/apps/customproperties/customproperties')
			const res = await axios.get(url)
			this.properties = res.data
		},
		async removeProperty(id) {
			const url = generateUrl(`/apps/customproperties/customproperties/${id}`)
			await axios.delete(url)
			await this.getDataFromApi()
		},
		async updateProperty(customProperty) {
			const url = generateUrl('/apps/customproperties/customproperties')
			await axios.post(url, { customProperty })
			await this.getDataFromApi()
		},
		async createProperty(property) {
			const propertylabel = property.propertylabel
			if (this.isBlank(propertylabel)) {
				showError(t('customproperties', 'Property has to have a name!'))
			}
			const url = generateUrl('/apps/customproperties/customproperties')
			await axios.put(url, {
				propertylabel,
			})
			await this.getDataFromApi()
			this.property.propertylabel = null
		},
		isBlank(str) {
			return (!str || /^\s*$/.test(str))
		},
	},
}
</script>
