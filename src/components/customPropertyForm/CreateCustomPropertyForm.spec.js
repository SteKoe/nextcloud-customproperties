import CreateCustomPropertyForm from './CreateCustomPropertyForm'
import userEvent from '@testing-library/user-event'
import { render } from '../../test-tools'
import { screen } from '@testing-library/vue'

describe('CreateCustomPropertyForm.vue', () => {
	it('should emit event to create CustomProperty', async() => {
		const { emitted } = await render(CreateCustomPropertyForm)

		fillOutPropertyForm('Text', 'name', 'label')

		userEvent.click(screen.getByLabelText('submit'))

		expect.assertions(1)
		expect(emitted().createProperty[0]).toContainEqual({
			id: null,
			propertytype: 'text',
			propertyname: 'name',
			propertylabel: 'label',
			propertyshared: false,
		})
	})

	it('submit should be omitted when form is not valid', async() => {
		const { emitted } = await render(CreateCustomPropertyForm)

		fillOutPropertyForm()

		userEvent.click(screen.getByLabelText('submit'))

		expect.assertions(1)
		expect(emitted().createProperty).toBeUndefined()
	})

	function fillOutPropertyForm(type = 'Text', name = null, label = null) {
		const selectPropertytype = screen.getByRole('combobox')
		userEvent.selectOptions(selectPropertytype, type)

		const nameInput = screen.getByLabelText('propertyname')
		userEvent.type(nameInput, name)

		const labelInput = screen.getByLabelText('propertylabel')
		userEvent.type(labelInput, label)
	}

})
