<template>
	<section class="section">
		<h2>{{ t('customproperties', 'Custom Properties') }}</h2>
		<p class="settings-hint">
			{{
				t('customproperties', 'Custom properties defined here are available to all users. They are shown in "Properties" tab in sidebar view. They can be accessed via WebDAV. Deleting properties will not wipe property values.')
			}}
		</p>
		<div>
			<CreateCustomPropertyForm @createProperty="createProperty" />

			<hr>
			<template v-for="property in properties">
				<EditCustomPropertyForm :key="property.id"
					:property="property"
					@deleteProperty="deleteProperty"
					@updateProperty="updateProperty" />
			</template>
		</div>
	</section>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import '@nextcloud/dialogs/styles/toast.scss'
import CreateCustomPropertyForm from './customPropertyForm/CreateCustomPropertyForm'
import EditCustomPropertyForm from './customPropertyForm/EditCustomPropertyForm'

export default {
	name: 'AdminSettings',
	components: {
		EditCustomPropertyForm,
		CreateCustomPropertyForm,
	},
	data() {
		return {
			icon: 'icon-info',
			loading: true,
			name: t('properties', 'Properties'),
			properties: [],
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
		async deleteProperty(id) {
			const url = generateUrl(`/apps/customproperties/customproperties/${id}`)
			await axios.delete(url)
			await this.getDataFromApi()
			showSuccess(this.t('customproperties', 'Custom Property has been deleted!'))
		},
		async updateProperty(customProperty) {
			const url = generateUrl('/apps/customproperties/customproperties')
			await axios.post(url, { customProperty })
			await this.getDataFromApi()
			showSuccess(this.t('customproperties', 'Custom Property has been changed!'))
		},
		async createProperty(customProperty) {
			const url = generateUrl('/apps/customproperties/customproperties')
			try {
				await axios.put(url, { customProperty })
				await this.getDataFromApi()
				showSuccess(this.t('customproperties', 'New Custom Property has been added!'))
			} catch (e) {
				console.error(e)
				showError(t('customproperties', 'Error saving property, please check constraints.'))
			}
		},
	},
}
</script>
