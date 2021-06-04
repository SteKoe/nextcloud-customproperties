<template>
	<div class="input-group">
		<input :value="property_.id"
			type="hidden">
		<div class="form-group">
			<select
				v-model="property_.propertytype"
				:disabled="disabled.includes('propertytype')"
				aria-label="propertytype"
				class="propertytype"
				required>
				<option v-for="type in propertytypes"
					:key="type.value"
					:value="type.value"
					v-text="t('customproperties', type.name)" />
			</select>
		</div>
		<div class="form-group">
			<input
				v-model="property_.propertyname"
				:disabled="disabled.includes('propertyname')"
				:placeholder="t('customproperties', 'Name...')"
				aria-label="propertyname"
				class="propertyname"
				pattern="^[a-z]+[a-z0-9]+$"
				required
				type="text">
		</div>
		<div class="form-group">
			<input
				v-model="property_.propertylabel"
				:disabled="disabled.includes('propertylabel')"
				:placeholder="t('customproperties', 'Label...')"
				aria-label="propertylabel"
				class="propertylabel"
				required
				type="text">
		</div>
	</div>
</template>

<script>
export default {
	name: 'CustomPropertyForm',
	model: {
		prop: 'property',
	},
	props: {
		property: {
			type: Object,
			required: true,
		},
		disabled: {
			type: Array,
			default: () => [],
		},
	},
	data() {
		return {
			property_: this.property,
			propertytypes: [
				{
					name: 'Text',
					value: 'text',
				},
				{
					name: 'URL / Link',
					value: 'url',
				},
				{
					name: 'Date',
					value: 'date',
				},
				{
					name: 'Number',
					value: 'number',
				},
			],
		}
	},
}
</script>

<style lang="css" scoped>
.form-horizontal .input-group {
  display: flex;
}

.propertylabel {
  width: 400px;
}
</style>
