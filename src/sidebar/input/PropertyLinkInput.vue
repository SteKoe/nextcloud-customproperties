<template>
	<div class="customproperty-form-group">
		<label :for="'property_'+property_.propertyname" v-text="property_.propertylabel" />
		<div class="customproperty-input-group">
			<input
				:id="'property_'+property_.propertyname"
				v-model="property_.propertyvalue"
				:name="property_.propertyname"
				class="customproperty-form-control"
				type="text"
				@blur="blur"
				@focus="focus">

			<div class="customproperty-input-group-append">
				<a class="customproperty-input-group-text icon-link"
					:href="property_.propertyvalue"
					target="_blank" />
			</div>
		</div>
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
