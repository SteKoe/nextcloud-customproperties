<template>
	<div :class="{ 'icon-loading': loading }">
		<PropertyList :properties="properties.knownProperties" @propertyChanged="updateProperty($event)" />

		<template v-if="properties.otherProperties.length > 0">
			<h3>{{ t('customproperties', 'WebDAV properties') }}</h3>
			<PropertyList :properties="properties.otherProperties" :disabled="true" />
		</template>
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateRemoteUrl, generateUrl } from '@nextcloud/router'
import { getCurrentUser } from '@nextcloud/auth'
import PropertyList from './PropertyList'

export default {
	name: 'Sidebar',
	components: { PropertyList },
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
	methods: {
		async update(fileInfo) {
			this.loading = true

			this.properties.knownProperties = []
			this.properties.otherProperties = []

			if (!isEmptyObject(fileInfo)) {
				this.fileInfo = fileInfo

				const properties = await this.retrieveProps()
				const customProperties = await this.retrieveCustomProperties()
				const customPropertyNames = customProperties.map(cp => cp.propertyname)

				this.properties.knownProperties = customProperties.map(customProperty => {
					const property = properties.find(p => customProperty.propertyname === p.propertyname)
					return {
						...property,
						...customProperty,
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
			}

			this.loading = false
		},
		async retrieveCustomProperties() {
			const customPropertiesUrl = generateUrl('/apps/customproperties/customproperties')
			const customPropertiesResponse = await axios.get(customPropertiesUrl)
			return customPropertiesResponse.data
		},
		async retrieveProps() {
			const uid = getCurrentUser().uid
			const path = `/files/${uid}/${this.fileInfo.path}/${this.fileInfo.name}`.replace(/\/+/ig, '/')
			const url = generateRemoteUrl('dav') + path
			const result = await axios.request({
				method: 'PROPFIND',
				url,
				data: '<d:propfind xmlns:d="DAV:"></d:propfind>',
			})

			return xmlToTagList(result.data)
		},
		async updateProperty(property) {
			const uid = getCurrentUser().uid
			const path = `/files/${uid}/${this.fileInfo.path}/${this.fileInfo.name}`.replace(/\/+/ig, '/')
			const url = generateRemoteUrl('dav') + path
			await axios.request({
				method: 'PROPPATCH',
				url,
				data: `
          <d:propertyupdate xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns">
           <d:set>
             <d:prop>
              <${property.propertyname}>${property.propertyvalue}</${property.propertyname}>
             </d:prop>
           </d:set>
          </d:propertyupdate>`,
			})
		},
	},
}

const parseXml = (xml) => {
	let dom = null
	try {
		dom = (new DOMParser()).parseFromString(xml, 'text/xml')
	} catch (e) {
		console.error('Failed to parse xml document', e)
	}
	return dom
}

const xmlToJson = (xml, documentElement) => {
	let obj = {}

	if (xml === null || xml === undefined) {
		return obj
	}

	if (xml.nodeType === 1) {
		obj['@prefix'] = xml.prefix
		obj['@namespaceURI'] = xml.prefix ? xml.lookupNamespaceURI(xml.prefix) : undefined

		if (xml.attributes.length > 0) {
			obj['@attributes'] = {}
			for (let j = 0; j < xml.attributes.length; j++) {
				const attribute = xml.attributes.item(j)
				obj['@attributes'][attribute.nodeName] = attribute.nodeValue
			}
		}
	} else if (xml.nodeType === 3) {
		obj = xml.nodeValue
	}

	if (xml.hasChildNodes()) {
		for (let i = 0; i < xml.childNodes.length; i++) {
			const item = xml.childNodes.item(i)
			const nodeName = item.nodeName
			if (typeof (obj[nodeName]) === 'undefined') {
				obj[nodeName] = xmlToJson(item, documentElement)
			} else {
				if (typeof obj[nodeName].push === 'undefined') {
					const old = obj[nodeName]
					obj[nodeName] = []
					obj[nodeName].push(old)
				}
				obj[nodeName].push(xmlToJson(item, documentElement))
			}
		}
	}
	return obj
}

const xmlToTagList = (xmlString) => {
	const xml = parseXml(xmlString)
	const json = xmlToJson(xml, xml)

	if (json['d:multistatus']) {
		const list = json['d:multistatus']['d:response']

		let listElement = list['d:propstat']
		listElement = Array.isArray(listElement) ? listElement : [listElement]
		return [...listElement]
			.filter(propstat => propstat['d:status']['#text'] === 'HTTP/1.1 200 OK')
			.map(tag => tag['d:prop'])
			.flatMap(tag => {
				return Object.entries(tag)
					.map(a => a)
					.filter(([nodeType, content]) => {
						return content.hasOwnProperty('@prefix')
					})
			})
			.map(([propertyname, value]) => {
				const namespaceURI = value['@namespaceURI']
				const propertyvalue = value['#text']
				const propertynameWithoutPrefix = propertyname.split(':').pop()
				return {
					propertyname,
					propertyvalue,
					namespaceURI,
				}
			})
	}

	return []
}

export const isEmptyObject = (fileInfo) => {
	if (fileInfo === null || fileInfo === undefined) {
		return true
	}
	return fileInfo && Object.keys(fileInfo).length === 0 && fileInfo.constructor === Object
}
</script>

<style lang="css">
.customproperty-input-group label {
  display: block;
}

.customproperty-input-group .customproperty-input {
  width: 100%;
}
</style>
