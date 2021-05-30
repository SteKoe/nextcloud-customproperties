<template>
	<div :class="{ 'icon-loading': loading }">
		<div v-for="(property, index) in properties.knownProperties" :key="index" class="input">
			<label :for="'property_'+property.propertyname">{{ property.propertylabel }}</label>
			<input :id="'property_'+property.propertyname"
				v-model="property.propertyvalue"
				:name="property.propertyname"
				type="text"
				placeholder=""
				@blur="blur">
		</div>

		<template v-if="properties.otherProperties.length > 0">
			<h3>
				{{ t('customproperties', 'Other Properties') }}
				<small>{{
					t('customproperties', 'Properties in this section have not been defined as custom property in the settings.')
				}}</small>
			</h3>
			<div v-for="(property, index) in properties.otherProperties" :key="index" class="input">
				<label :for="'property_'+property.propertyname">{{ property.propertylabel }}</label>
				<input :id="'property_'+property.propertyname"
					v-model="property.propertyvalue"
					:name="property.propertyname"
					type="text"
					placeholder=""
					@blur="blur">
			</div>
		</template>
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { getCurrentUser } from '@nextcloud/auth'
import { generateUrl } from '@nextcloud/router'

export default {
	name: 'Sidebar',
	data() {
		return {
			fileInfo: {},
			loading: true,
			properties: {
				knownProperties: [],
				otherProperties: [],
			},
		}
	},
	watch: {
		fileInfo(newFile, oldFile) {
			if (newFile.id !== oldFile.id) {
				this.update(newFile)
			}
		},
	},
	methods: {
		async update(fileInfo) {
			this.fileInfo = fileInfo
			const url = generateUrl('/apps/customproperties/properties')
			const response = await axios.get(url, {
				params: {
					id: fileInfo.id,
					path: fileInfo.path,
					name: fileInfo.name,
				},
			})
			const data = response.data
			const knownProperties = data.filter(property => property._knownproperty === true)
			const otherProperties = data.filter(property => property._knownproperty === false)

			this.properties = {
				knownProperties,
				otherProperties: otherProperties.sort((a, b) => a.propertyname.localeCompare(b.propertyname)),
			}

			this.loading = false
		},
		async blur($event) {
			const url = generateUrl('/apps/customproperties/properties')
			const fileInfo = this.fileInfo
			const path = ['files', getCurrentUser().uid, fileInfo.path, fileInfo.name].join('/').replace(/\/{2,}/g, '/')

			await axios.post(url, {
				propertypath: path,
				propertyname: $event.target.name,
				propertyvalue: $event.target.value,
			})
		},
	},
}
</script>

<style lang="css" scoped>
label {
	display: block;
}

input[type='text'] {
	width: 100%;
}
</style>
