<template>
	<div>
		<div v-for="(customProperty, index) in properties" :key="index" class="input-group">
			<label class="customproperty-label" :for="'propertylabel_'+customProperty.id" v-text="customProperty.propertynameWithNamespace" />
			<div class="form-control-wrapper">
				<input :id="'propertylabel_'+customProperty.id"
					v-model="customProperty.propertylabel"
					class="customproperty-name"
					type="text"
					:name="'propertylabel[' + customProperty.id + ']'">
				<button type="button" class="button button-delete" @click="$emit('remove', customProperty.id)">
					{{ t('customproperties', 'Delete') }}
				</button>
				<button type="button" class="button button-save" @click="$emit('update', customProperty)">
					{{ t('customproperties', 'Update') }}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'CustomPropertyList',
	props: {
		properties: {
			type: Array,
			required: true,
		},
	},
}
</script>

<style lang="css" scoped>
.form-control-wrapper {
	display: flex;
	align-items: baseline;
}

.customproperty-label {
	width: 100%;
	max-width: 150px;
	text-align: right;
	padding-right: 10px;
}

.customproperty-name {
	width: 100%;
	max-width: 400px;
}
</style>
