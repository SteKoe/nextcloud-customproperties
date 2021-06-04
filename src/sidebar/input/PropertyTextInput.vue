<template>
	<div class="customproperty-form-group">
		<label :for="'property_'+property_.propertyname">{{ property_.propertylabel }}</label>

		<div class="customproperty-input-group">
			<input :id="'property_'+property_.propertyname"
				v-model="property_.propertyvalue"
				:aria-disabled="disabled"
				:disabled="disabled"
				:name="property_.propertyname"
				:type="property_.propertytype"
				class="customproperty-form-control"
				@blur="blur"
				@focus="focus">
		</div>
	</div>
</template>

<script>
export default {
	name: 'PropertyTextInput',
	model: {
		prop: 'property',
	},
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
			property_: this.property,
			value: undefined,
		}
	},
	methods: {
		focus() {
			this.value = this.property.propertyvalue
		},
		blur() {
			if (this.value !== this.property.propertyvalue) {
				this.$emit('blur', this.property)
			}
		},
	},
}
</script>
