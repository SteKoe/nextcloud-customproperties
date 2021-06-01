<template>
	<div class="input">
		<label :for="'property_'+property.propertyname">{{ property.propertylabel }}</label>
		<input :id="'property_'+property.propertyname"
			v-model="property.propertyvalue"
			:name="property.propertyname"
			type="text"
			:disabled="disabled"
			:aria-disabled="disabled"
			@focus="focus"
			@blur="blur">
	</div>
</template>

<script>
export default {
	name: 'PropertyItem',
	model: {
		prop: 'property',
		event: 'change',
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

<style lang="css" scoped>
label {
  display: block;
}

input[type='text'] {
  width: 100%;
}
</style>
