<template>
	<div :class="{ 'icon-loading': loading }">
		<div v-show="!loading">
			<h3>{{ t('customproperties', 'Custom Properties') }}</h3>
			<PropertyList :properties="properties.knownProperties" @propertyChanged="updateProperty($event)" />
			<EmptyPropertiesPlaceholder v-if="properties.knownProperties.length === 0" />

			<template v-if="properties.otherProperties.length > 0">
				<h3>{{ t('customproperties', 'WebDAV properties') }}</h3>
				<PropertyList :disabled="true" :properties="properties.otherProperties" />
			</template>
		</div>
	</div>
</template>

<script>
import PropertyList from './PropertyList'
import EmptyPropertiesPlaceholder from '../../components/emptypropertiesplaceholder/EmptyPropertiesPlaceholder'
import { isEmptyObject, xmlToTagList } from './utils'
import { generateRemoteUrl, generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { getCurrentUser } from '@nextcloud/auth'

export default {
	name: 'TabContent',
	components: {
		EmptyPropertiesPlaceholder,
		PropertyList,
	},
	props: {
		fileInfo: {
			type: Object,
			default: () => {},
		},
	},
	data() {
	  return {
			loading: true,
			fileInfo_: this.fileInfo,
			properties: {
				knownProperties: [],
				otherProperties: [],
			},
		}
	},
	async mounted() {
		await this.update()
	},
	methods: {
	  async updateFileInfo(fileInfo) {
	    this.fileInfo_ = fileInfo
			await this.update()
		},
		async update() {
			this.properties.knownProperties = []
			this.properties.otherProperties = []
			if (!isEmptyObject(this.fileInfo_)) {
				this.loading = true

				const properties = await this.retrieveProps(this.fileInfo_)
				const customProperties = await this.retrieveCustomProperties()
				const customPropertyNames = customProperties.map(cp => `${cp.prefix}:${cp.propertyname}`)

				this.properties.knownProperties = customProperties.map(cp => {
					const property = properties.find(p => `${cp.prefix}:${cp.propertyname}` === p.propertyname)
					return {
						...property,
						...cp,
					}
				})

				this.properties.otherProperties = properties
					.filter(property => {
						return !customPropertyNames.includes(property.propertyname)
					})
					.map(property => {
						return {
							propertylabel: property.propertyname,
							...property,
						}
					})

				this.loading = false
			}
		},
		async retrieveCustomProperties() {
			try {
				const customPropertiesUrl = generateUrl('/apps/customproperties/customproperties')
				const customPropertiesResponse = await axios.get(customPropertiesUrl)
				return customPropertiesResponse.data
			} catch (e) {
				console.error(e)
				return []
			}
		},
		async retrieveProps(fileInfo) {
			try {
				const uid = getCurrentUser().uid
				const path = `/files/${uid}/${fileInfo.path}/${fileInfo.name}`.replace(/\/+/ig, '/')
				const url = generateRemoteUrl('dav') + path
				const result = await axios.request({
					method: 'PROPFIND',
					url,
					data: '<d:propfind xmlns:d="DAV:"></d:propfind>',
				})

				return xmlToTagList(result.data)
			} catch (e) {
				console.error(e)
				return []
			}
		},
		async updateProperty(property) {
			const uid = getCurrentUser().uid
			const path = `/files/${uid}/${this.fileInfo_.path}/${this.fileInfo_.name}`.replace(/\/+/ig, '/')
			const url = generateRemoteUrl('dav') + path
			const propTag = `${property.prefix}:${property.propertyname}`
			try {
				await axios.request({
					method: 'PROPPATCH',
					url,
					data: `
            <d:propertyupdate xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns">
             <d:set>
               <d:prop>
                <${propTag}>${property.propertyvalue}</${propTag}>
               </d:prop>
             </d:set>
            </d:propertyupdate>`,
				})
			} catch (e) {
				console.error(e)
			}
		},
	},
}
</script>

<style lang="scss">
.customproperty-input-group {
  display: flex;
  align-items: stretch;
}

.customproperty-input-group-append {
  display: flex;
  margin-left: -1px;
}

.customproperty-input-group-text {
  display: flex;
  align-items: center;
  padding: 0 1rem;
  margin-bottom: 0;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #495057;
  text-align: center;
  white-space: nowrap;
  background-color: #e9ecef;
  border: 1px solid #ced4da;
  border-radius: .25rem;
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}

.customproperty-form-control {
  flex: 1 1 auto;
  margin: 0 !important;
}

.customproperty-form-group {
  > label {
    display: block;
  }
}
</style>
