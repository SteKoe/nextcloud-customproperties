import { screen } from '@testing-library/vue'
import PropertyLinkInput from './PropertyLinkInput'
import { render } from '../../test-tools'

describe('PropertyList.vue', () => {
	async function renderWithProperty(property) {
		await render(PropertyLinkInput, {
			props: {
				property,
			},
		})
	}

	test('renders text', async() => {
		await renderWithProperty({
			id: 1,
			propertyname: 'name',
			propertylabel: 'label',
			propertyvalue: 'https://www.codecentric.de/',
			propertytype: 'url',
		})

		const inputFieldValue = screen.getByLabelText('label').value
		const linkButton = screen.getByLabelText('follow-link').href

		expect(inputFieldValue).toEqual('https://www.codecentric.de/')
		expect(linkButton).toEqual('https://www.codecentric.de/')
	})

	test('link is disabled when no value is present', async() => {
		await renderWithProperty({
			id: 1,
			propertyname: 'name',
			propertylabel: 'label',
			propertyvalue: undefined,
			propertytype: 'url',
		})

		const linkButton = screen.getByLabelText('follow-link')

		expect(linkButton.href).toEqual('')
	})

})
