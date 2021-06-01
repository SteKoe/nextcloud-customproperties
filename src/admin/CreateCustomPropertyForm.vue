<template>
	<div class="input-group">
		<select v-model="property.propertytype" class="customproperty-type">
			<option v-for="type in propertytypes"
				:key="type.value"
				:value="type.value"
				v-text="t('customproperties', type.name)" />
		</select>
		<input v-model="property.propertyname"
			pattern="[a-z]"
			class="customproperty-name"
			type="text"
			:placeholder="t('customproperties', 'Name...')">
		<input v-model="property.propertylabel"
			class="customproperty-label"
			type="text"
			:placeholder="t('customproperties', 'Label...')">
		<button type="submit"
			class="button"
			@click="$emit('createProperty', property)"
			v-text="t('customproperties', 'Add')" />
	</div>
</template>

<script>
export default {
	name: 'CreateCustomPropertyForm',
	data() {
		return {
			property: {
			  propertyname: null,
			  propertylabel: null,
				propertytype: 'text',
			},
			propertytypes: [
				{ name: 'Text', value: 'text' },
				{ name: 'URL / Link', value: 'url' },
				{ name: 'Date', value: 'date' },
				{ name: 'Number', value: 'number' },
			],
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
