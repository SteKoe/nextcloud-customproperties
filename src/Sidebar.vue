<template>
	<Tab :id="id" :icon="icon" :name="name" :class="{ 'icon-loading': loading }">
		<div class="input" v-for="property in properties.knownProperties">
			<label :for="'property_'+property.propertyname">{{ property.propertylabel }}</label>
			<input :id="'property_'+property.propertyname" :name="property.propertyname" type="text" placeholder="" v-model="property.propertyvalue" @blur="blur">
		</div>

		<template v-if="properties.otherProperties.length > 0">
			<h3>
				{{ t('customproperties', 'Other Properties') }}
				<small>{{ t('customproperties', 'Properties in this section have not been defined as custom property in the settings.') }}</small>
			</h3>
			<div class="input" v-for="property in properties.otherProperties">
				<label :for="'property_'+property.propertyname">{{ property.propertylabel }}</label>
				<input :id="'property_'+property.propertyname" :name="property.propertyname" type="text" placeholder="" v-model="property.propertyvalue" @blur="blur">
			</div>
		</template>
	</Tab>
</template>

<script>
	import axios from '@nextcloud/axios'
	import Actions from '@nextcloud/vue/dist/Components/Actions'
	import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
	import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
	import Tab from '@nextcloud/vue/dist/Components/AppSidebarTab'

	export default {
		name: 'Sidebar',
		components: {
			Actions,
			ActionButton,
			ActionInput,
			Tab
		},
		props: {
			fileInfo: {
				type: Object,
				default: () => {
				},
				required: true
			}
		},
		data () {
			return {
				icon: 'icon-info',
				loading: true,
				name: t('properties', 'Properties'),
				properties: {
					knownProperties: [],
					otherProperties: []
				}
			}
		},
		created () {
			this.getDataFromApi()
		},
		computed: {
			/**
			 * Needed to differenciate the tabs
			 * pulled from the AppSidebarTab component
			 *
			 * @returns {string}
			 */
			id () {
				return this.name.toLowerCase().replace(/ /g, '-')
			},

			/**
			 * Returns the current active tab
			 * needed because AppSidebarTab also uses $parent.activeTab
			 *
			 * @returns {string}
			 */
			activeTab () {
				return this.$parent.activeTab
			}
		},
		methods: {
			async getDataFromApi () {
				const url = OC.generateUrl(`/apps/customproperties/properties`);
				const fi = this.fileInfo;

				const response = await axios.get(url, {
					params: {
						id: fi.id,
						path: fi.path,
						name: fi.name
					}
				});
				let data = response.data;
				let knownProperties = data.filter(property => property['_knownproperty'] === true);
				let otherProperties = data.filter(property => property['_knownproperty'] === false);

				this.properties = {
					'knownProperties': knownProperties,
					'otherProperties': otherProperties.sort((a,b) => a.propertyname.localeCompare(b.propertyname))
				};

				this.loading = false;
			},
			async blur ($event) {
				const url = OC.generateUrl(`/apps/customproperties/properties`);
				const fileInfo = this.fileInfo;
				const path = ['files', OC.getCurrentUser().uid, fileInfo.path, fileInfo.name].join('/').replace(/\/{2,}/g, '/');

				await axios.post(url, {
					propertypath: path,
					propertyname: $event.target.name,
					propertyvalue: $event.target.value
				});
			}
		}
	}
</script>
