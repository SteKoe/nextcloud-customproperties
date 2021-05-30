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
				t('customproperties', 'Deleting properties will not wipe already set properties. Properties set will be still accessible via WebDAV.')
			}}
		</p>
		<form @submit="createProperty">
			<div class="form-group">
				<div v-for="(customProperty, index) in properties" :key="index" class="input-group">
					<div class="form-control-wrapper">
						<label class="form-control-label" :for="'propertylabel_'+customProperty.id">
							<small>
								oc:{{ customProperty.propertyname }}
							</small>
						</label>
						<input :id="'propertylabel_'+customProperty.id"
							v-model="customProperty.propertylabel"
							class="form-control"
							type="text"
							:name="'propertylabel[' + customProperty.id + ']'">
					</div>
					<button type="button" class="button button-delete" @click="removeProperty(customProperty.id)">
						{{ t('customproperties', 'Delete') }}
					</button>
				</div>
				<hr>
				<div class="input-group">
					<input v-model="property.propertylabel"
						class="form-control form-control__label"
						type="text"
						:placeholder="t('customproperties', 'Create property...')">
					<button type="submit" class="button">
						{{ t('customproperties', 'Save') }}
					</button>
				</div>
			</div>
		</form>
	</section>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'
import '@nextcloud/dialogs/styles/toast.scss'

export default {
	name: 'AdminSettings',
	components: {},
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
		async createProperty(e) {
			e.preventDefault()

			const propertylabel = this.property.propertylabel
			if (this.isBlank(propertylabel)) {
				showError(t('customproperties', 'Property has to have a name!'))
				return
			}

			const url = generateUrl('/apps/customproperties/customproperties')
			await axios.post(url, {
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
