<template>
	<div class="customproperty-input-group">
		<label :for="'property_'+property_.propertyname" v-text="property_.propertylabel" />

		<div v-show="!isInEditMode">
			<a :href="property_.propertyvalue" target="_blank" v-text="property_.propertyvalue" />
		</div>

		<input v-show="isInEditMode"
			:id="'property_'+property_.propertyname"
			v-model="property_.propertyvalue"
			:aria-disabled="disabled"
			:disabled="disabled"
			:name="property_.propertyname"
			class="customproperty-input"
			type="text"
			@blur="blur"
			@focus="focus">
	</div>
</template>

<script>
export default {
	name: 'PropertyLinkInput',
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
			value: undefined,
			property_: this.property,
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
