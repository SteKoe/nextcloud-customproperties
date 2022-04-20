<template>
	<div class="customproperty-form-group">
		<label :for="'property'+property.propertyname">{{ property.propertylabel }}</label>
		<div class="customproperty-input-group">
			<input :id="'property'+property.propertyname"
				v-model="value"
				:aria-disabled="disabled"
				:disabled="disabled"
				:name="property.propertyname"
				:type="type"
				:step="stepSize"
				class="customproperty-form-control"
				@blur="blur">
		</div>
	</div>
</template>

<script>
export default {
	name: 'PropertyTextInput',
	props: {
		property: {
			type: Object,
			required: true,
		},
		disabled: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			value: this.property.propertyvalue,
		}
	},
	computed: {
		stepSize() {
			return this.property.propertytype?.toLowerCase() === 'decimal' ? 'any' : null
		},
		type() {
			if (this.property.propertytype) {
				const propertyType = this.property.propertytype.toLowerCase()
				switch (propertyType) {
				case 'decimal':
					return 'number'
				default:
					return propertyType
				}
			}

			return 'text'
		},
	},
	methods: {
		blur() {
			this.$emit('blur', {
				...this.property,
				propertyvalue: this.value,
			})
		},
	},
}
</script>
