<template>
	<div>
		<template v-for="(property, index) in properties">
			<PropertyLinkInput
				v-if="isType(property.propertytype, 'url')"
				:key="index"
				:disabled="disabled"
				:property="property"
				@blur="$emit('propertyChanged', $event)" />
			<PropertyTextInput
				v-else
				:key="index"
				:disabled="disabled"
				:property="property"
				@blur="$emit('propertyChanged', $event)" />
		</template>
	</div>
</template>

<script>
import PropertyTextInput from './input/PropertyTextInput'
import PropertyLinkInput from './input/PropertyLinkInput'

export default {
	name: 'PropertyList',
	components: {
		PropertyLinkInput,
		PropertyTextInput,
	},
	props: {
		properties: {
			type: Array,
			required: true,
		},
		disabled: {
			type: Boolean,
			default: false,
		},
	},
	methods: {
		isType(typeOfProperty, ...types) {
			return types.includes(typeOfProperty)
		},
	},
}
</script>
