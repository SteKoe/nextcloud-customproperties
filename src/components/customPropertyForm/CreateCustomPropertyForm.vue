<template>
	<form class="form-horizontal" @submit="submit">
		<CustomPropertyForm v-model="property" />
		<button
			aria-label="submit"
			class="button"
			type="submit"
			v-text="t('customproperties', 'Add')" />
	</form>
</template>

<script>
import CustomPropertyForm from './CustomPropertyForm'
import { alpha, required } from 'vuelidate/lib/validators'
import { validationMixin } from 'vuelidate'
import { showError } from '@nextcloud/dialogs'

export default {
	components: { CustomPropertyForm },
	mixins: [validationMixin],
	data() {
		return {
			errors: [],
			property: {
				id: null,
				propertyname: null,
				propertylabel: null,
				propertytype: 'text',
				propertyshared: false,
			},
		}
	},
	methods: {
		submit(e) {
			e.preventDefault()

			if (this.$v.$invalid || this.$v.error) {
				showError(this.t('customproperties', 'Cannot create Custom Property. The given input is invalid.'))
				return
			}

			this.$emit('createProperty', this.property)
			this.property = {
				propertyname: null,
				propertylabel: null,
				propertytype: 'text',
				propertyshared: false,
			}
		},
		isBlank(str) {
			return (!str || /^\s*$/.test(str))
		},
	},
	validations: {
		property: {
			propertytype: {
				required,
			},
			propertyname: {
				required,
				alpha,
			},
			propertylabel: {
				required,
			},
		},
	},
}
</script>

<style lang="scss">
.form-horizontal {
  display: flex;
}
</style>
