<template>
	<div class="input-group">
		<input :value="property_.id"
			type="hidden">
		<div class="form-group form-group__propertytype">
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
		<div class="form-group form-group__propertyname">
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
		<div class="form-group form-group__propertylabel">
			<input
				v-model="property_.propertylabel"
				:disabled="disabled.includes('propertylabel')"
				:placeholder="t('customproperties', 'Label...')"
				aria-label="propertylabel"
				class="propertylabel"
				required
				type="text">
		</div>
		<div class="form-group form-group__propertyshared">
			<select
				v-model="property_.propertyshared"
				:disabled="disabled.includes('propertyshared')"
				aria-label="propertyshared"
				class="propertyshared"
				required>
				<option
					:key="false"
					:value="false"
					v-text="t('private', 'Private')" />
				<option
					:key="true"
					:value="true"
					v-text="t('shared', 'Shared')" />
			</select>
		</div>
	</div>
</template>

<script>
export default {
	name: 'CustomPropertyForm',
	model: {
		prop: 'property',
		event: 'change',
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
			propertytypes: [
				{ name: 'Text', value: 'text' },
				{ name: 'URL / Link', value: 'url' },
				{ name: 'Date', value: 'date' },
				{ name: 'Date-Time', value: 'datetime-local' },
				{ name: 'Week', value: 'week' },
				{ name: 'Month', value: 'month' },
				{ name: 'Time', value: 'time' },
				{ name: 'Number', value: 'number' },
			],
		}
	},
	computed: {
	  property_() {
	    return this.property
		},
	},
}
</script>

<style lang="scss" scoped>
.form-horizontal .input-group {
  display: flex;
}

.propertytype { margin-right: 3px; }

.propertytype, .propertyname, .propertylabel, .propertyshared {
  width: 100%;
}

.form-group {
  flex: 1 1 0;
  margin-right: 3px;

  &__propertytype {
    flex: 0 0 75px;
  }

  &__propertyname {
    flex: 0 0 50px;
  }

  &__propertylabel {
    flex: 4 0 0;
  }

  &__propertyshared {
    flex: 0 0 95px;
  }
}
</style>
