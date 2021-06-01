<template>
	<div class="input-group">
		<input v-model="property.propertylabel"
			class="customproperty-label"
			type="text"
			:placeholder="t('customproperties', 'Create property...')">
		<button type="submit" class="button" @click="$emit('createProperty', property)">
			{{ t('customproperties', 'Add') }}
		</button>
	</div>
</template>

<script>
export default {
	name: 'CreateCustomPropertyForm',
	data() {
		return {
			property: {
			  propertylabel: null,
			},
		}
	},
}
</script>

<style lang="css" scoped>
.customproperty-label {
	width: 100%;
	max-width: 400px;
}
</style>
