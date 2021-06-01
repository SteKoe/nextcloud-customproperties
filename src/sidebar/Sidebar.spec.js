import Sidebar, { isEmptyObject } from './Sidebar'
import { createLocalVue, shallowMount } from '@vue/test-utils'
import axios from '@nextcloud/axios'
import * as auth from '@nextcloud/auth'
import * as router from '@nextcloud/router'

jest.mock('@nextcloud/axios')
jest.mock('@nextcloud/auth')
jest.mock('@nextcloud/router')

auth.getCurrentUser.mockReturnValue({ uid: 'stekoe' })
router.generateRemoteUrl.mockImplementation((service) => service)

describe('Sidebar.vue', () => {
	let localVue
	let wrapper

	beforeEach(() => {
		localVue = createLocalVue()
		wrapper = shallowMount(Sidebar, { localVue })
	})

	test.each`
		obj
		${{}}
		${null}
		${undefined}
	`('"$obj" should be identified as empty object', ({ obj }) => {
		expect(isEmptyObject(obj)).toBeTruthy()
	})

	test.each`
		fileInfo
		${{}}
		${null}
		${undefined}
	`('omit server call when "$fileInfo" is provided as "fileInfo"', async({ fileInfo }) => {
		await wrapper.vm.update(fileInfo)

		expect.assertions(2)
		expect(axios.request).toHaveBeenCalledTimes(0)
		expect(axios.get).toHaveBeenCalledTimes(0)
	})

	test('when fileInfo is provided, server call is issued', async() => {
		axios.get.mockResolvedValue({ data: [] })
		axios.request.mockResolvedValue({ data: {} })

		await wrapper.vm.update({ name: 'example.txt' })

		expect.assertions(2)
		expect(axios.get).toHaveBeenCalledTimes(1)
		expect(axios.request).toHaveBeenCalledTimes(1)
	})

	test('PROPFIND returns mapped JSON result for existing properties', async() => {
		const propStats = `
			<d:propstat>
				<d:prop>
					<oc:id>id</oc:id>
					<oc:name>name</oc:name>
					<oc:b/>
				</d:prop>
				<d:status>HTTP/1.1 200 OK</d:status>
			</d:propstat>
			<d:propstat>
				<d:prop>
					<oc:display-name/>
				</d:prop>
				<d:status>HTTP/1.1 404 Not Found</d:status>
			</d:propstat>`

		axios.request.mockResolvedValue({
			data: mockedPropFindXmlResponse(propStats),
		})

		const newVar = await wrapper.vm.retrieveProps({ file: 'example.txt' })

		expect.assertions(1)
		expect(newVar).toEqual([
			{
				propertyname: 'oc:id',
				propertyvalue: 'id',
				namespaceURI: 'http://owncloud.org/ns',
				propertynameWithNamespace: '{http://owncloud.org/ns}id',
			},
			{
				propertyname: 'oc:name',
				namespaceURI: 'http://owncloud.org/ns',
				propertyvalue: 'name',
				propertynameWithNamespace: '{http://owncloud.org/ns}name',
			},
			{
				propertyname: 'oc:b',
				namespaceURI: 'http://owncloud.org/ns',
				propertyvalue: undefined,
				propertynameWithNamespace: '{http://owncloud.org/ns}b',
			},
		])
	})

	test('PROPFIND returns empty list, when no properties were found', async() => {
		const propStats = `
		<d:propstat>
            <d:prop>
                <oc:display-name/>
            </d:prop>
            <d:status>HTTP/1.1 404 Not Found</d:status>
        </d:propstat>`

		axios.request.mockResolvedValue({
			data: mockedPropFindXmlResponse(propStats),
		})

		const newVar = await wrapper.vm.retrieveProps({ file: 'example.txt' })

		expect.assertions(1)
		expect(newVar).toEqual([])
	})
})

function mockedPropFindXmlResponse(propStats) {
	return `
<d:multistatus xmlns:d="DAV:" xmlns:s="http://sabredav.org/ns" xmlns:oc="http://owncloud.org/ns" xmlns:nc="http://nextcloud.org/ns">
    <d:response>
        <d:href>/remote.php/dav/files/admin/welcome.txt</d:href>
		${propStats}
    </d:response>
</d:multistatus>`
}
