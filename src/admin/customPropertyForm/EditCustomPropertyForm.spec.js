import userEvent from '@testing-library/user-event'
import { render } from '../../test-tools'
import { screen } from '@testing-library/vue'
import EditCustomPropertyForm from './EditCustomPropertyForm'

describe('EditCustomPropertyForm.vue', () => {
	let property

	beforeEach(() => {
		property = {
			id: 1,
			propertytype: 'text',
			propertyname: 'prename',
			propertylabel: 'prelabel',
		}
	})

	it('should disable propertytype', async() => {
		await render(EditCustomPropertyForm, { props: { property } })

		const input = screen.getByLabelText('propertytype')
		expect.assertions(1)
		expect(input.disabled).toBeTruthy()
	})

	it('should disable propertyname', async() => {
		await render(EditCustomPropertyForm, { props: { property } })

		const input = screen.getByLabelText('propertyname')
		expect.assertions(1)
		expect(input.disabled).toBeTruthy()
	})

	it('should emit event to update CustomProperty', async() => {
		const { emitted } = await render(EditCustomPropertyForm, { props: { property } })

		fillOutPropertyForm('Text', 'name', 'label')

		userEvent.click(screen.getByLabelText('update'))

		expect.assertions(1)
		expect(emitted().updateProperty[0]).toContainEqual({
			id: 1,
			propertytype: 'text',
			propertyname: 'prename',
			propertylabel: 'label',
		})
	})

	it('submit should be omitted when form is not valid', async() => {
		const { emitted } = await render(EditCustomPropertyForm, { props: { property } })

		fillOutPropertyForm()

		userEvent.click(screen.getByLabelText('update'))

		expect.assertions(1)
		expect(emitted().updateProperty).toBeUndefined()
	})

	it('send delete event when delete button is clicked', async() => {
		const { emitted } = await render(EditCustomPropertyForm, { props: { property } })

		userEvent.click(screen.getByLabelText('delete'))

		expect.assertions(1)
		expect(emitted().deleteProperty[0]).toContainEqual(1)
	})

	function fillOutPropertyForm(type = 'Text', name = null, label = null) {
		const selectPropertytype = screen.getByRole('combobox')
		userEvent.selectOptions(selectPropertytype, type)

		const nameInput = screen.getByLabelText('propertyname')
		userEvent.clear(nameInput)
		userEvent.type(nameInput, name)

		const labelInput = screen.getByLabelText('propertylabel')
		userEvent.clear(labelInput)
		userEvent.type(labelInput, label)
	}

})
