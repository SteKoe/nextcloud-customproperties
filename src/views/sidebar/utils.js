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

export const xmlToTagList = (xmlString) => {
	const xml = parseXml(xmlString)
	const json = xmlToJson(xml, xml)

	if (json['d:multistatus']) {
		const list = json['d:multistatus']['d:response']

		let listElement
		if (Array.isArray(list)) {
			listElement = list[0]['d:propstat']
		} else {
			listElement = list['d:propstat']
		}

		listElement = Array.isArray(listElement) ? listElement : [listElement]
		return [...listElement]
			.filter(propstat => propstat['d:status']['#text'] === 'HTTP/1.1 200 OK')
			.map(tag => tag['d:prop'])
			.flatMap(tag => {
				return Object.entries(tag)
					.map(a => a)
					.filter(([nodeType, content]) => {
						return content['@prefix'] !== undefined
					})
			})
			.map(([propertyname, value]) => {
				const namespaceURI = value['@namespaceURI']
				const propertyvalue = value['#text']

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
