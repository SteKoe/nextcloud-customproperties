<template>
	<form class="form-horizontal" @submit="submit">
		<CustomPropertyForm v-model="property_" :disabled="['propertytype', 'propertyname']" />
		<button
			aria-label="update"
			class="button"
			type="submit"
			v-text="t('customproperties', 'Update')" />

		<button
			aria-label="delete"
			class="button"
			type="button"
			@click="$emit('deleteProperty', property_.id)"
			v-text="t('customproperties', 'Delete')" />
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
	model: {
		prop: 'property',
		event: 'change',
	},
	props: {
		property: {
			type: Object,
			required: true,
		},
	},
	computed: {
		property_() {
			return this.property
		},
	},
	methods: {
		submit(e) {
			e.preventDefault()

			if (this.$v.$invalid || this.$v.error) {
				showError(this.t('customproperties', 'Cannot create Custom Property. The given input is invalid.'))
				return
			}

			this.$emit('updateProperty', this.property)
		},
		isBlank(str) {
			return (!str || /^\s*$/.test(str))
		},
	},
	validations: {
		property: {
			id: {
				required,
			},
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
