<template>
	<div class="customproperty-input-group">
		<label :for="'property_'+property.propertyname">{{ property.propertylabel }}</label>

		<div v-show="!isInEditMode">
			<a :href="property.propertyvalue" target="_blank" v-text="property.propertyvalue" />
		</div>

		<input v-show="isInEditMode"
			:id="'property_'+property.propertyname"
			v-model="property.propertyvalue"
			class="customproperty-input"
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
	name: 'PropertyLinkInput',
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
			isInEditMode: this.property.propertyvalue === undefined,
		}
	},
	methods: {
	  setEditMode() {
			const propertyvalue = this.property.propertyvalue
			this.isInEditMode = propertyvalue === undefined || propertyvalue.trim() === ''
		},
		focus() {
			this.value = this.property.propertyvalue
		},
		blur() {
			if (this.value !== this.property.propertyvalue) {
			  this.setEditMode()
				this.$emit('blur', this.property)
			}
		},
	},
}
</script>
