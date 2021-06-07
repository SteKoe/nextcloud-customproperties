<template>
	<div class="customproperty-form-group">
		<label :for="'property_'+property_.propertyname" v-text="property_.propertylabel" />
		<div class="customproperty-input-group">
			<input
				:id="'property_'+property_.propertyname"
				v-model="property_.propertyvalue"
				:name="property_.propertyname"
				class="customproperty-form-control"
				type="url"
				@blur="blur">

			<div class="customproperty-input-group-append">
				<div class="customproperty-input-group-text">
					<a class="customproperty-link icon-link"
						aria-label="follow-link"
						:href="property_.propertyvalue"
						target="_blank" />
				</div>
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
			property_: this.property,
		}
	},
	methods: {
		blur() {
			this.$emit('blur', this.property_)
		},
	},
}
</script>

<style lang="scss" scoped>
.customproperty-link[href=''],
.customproperty-link:not([href]) {
  opacity: 0.2;
}
</style>
