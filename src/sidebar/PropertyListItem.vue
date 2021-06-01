<template>
	<div>
		<div v-for="(property, index) in properties" :key="index" class="input">
			<label :for="'property_'+property.propertyname">{{ property.propertylabel }}</label>
			<input :id="'property_'+property.propertyname"
				v-model="property.propertyvalue"
				:name="property.propertyname"
				type="text"
				placeholder=""
				@blur="$emit('blur')">
		</div>
	</div>
</template>

<script>
export default {
	name: 'PropertyListItem',
	props: {
		properties: { type: Array, required: true },
	},
}
</script>
