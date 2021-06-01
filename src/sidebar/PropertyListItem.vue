<template>
	<div>
		<PropertyItem v-for="(property, index) in properties"
			:key="index"
			:property="property"
			:disabled="disabled"
			@blur="$emit('propertyChanged', $event)" />
	</div>
</template>

<script>
import PropertyItem from './PropertyItem'
export default {
	name: 'PropertyListItem',
	components: { PropertyItem },
	props: {
		properties: { type: Array, required: true },
		disabled: { type: Boolean, default: false },
	},
}
</script>
