<template>
	<div :class="{ 'icon-loading': loading }">
		<PropertyListItem :properties="properties.knownProperties" />

		<template v-if="properties.otherProperties.length > 0">
			<h3>{{ t('customproperties', 'WebDAV properties') }}</h3>
			<PropertyListItem :properties="properties.otherProperties" />
		</template>
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { getCurrentUser } from '@nextcloud/auth'
import { generateUrl, generateRemoteUrl } from '@nextcloud/router'
import PropertyListItem from './PropertyListItem'

export default {
	name: 'Sidebar',
	components: { PropertyListItem },
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

			const result = await axios({
				method: 'PROPFIND',
				url: generateRemoteUrl('dav') + `/files/${getCurrentUser().uid}/${fileInfo.path}/${fileInfo.name}`,
				data: `<?xml version="1.0"?>
					<d:propfind  xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns">
					</d:propfind>`,
			})

			console.log(result.data)
			// const response = await axios.get(url, {
			// 	params: {
			// 		id: fileInfo.id,
			// 		path: fileInfo.path,
			// 		name: fileInfo.name,
			// 	},
			// })
			// const data = response.data
			// const knownProperties = data.filter(property => property._knownproperty === true)
			// const otherProperties = data.filter(property => property._knownproperty === false)
			//
			// this.properties = {
			// 	knownProperties,
			// 	otherProperties: otherProperties.sort((a, b) => a.propertyname.localeCompare(b.propertyname)),
			// }

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
