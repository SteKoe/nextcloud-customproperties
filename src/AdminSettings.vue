<template>
	<section class="section">
		<h2>{{ t('customproperties', 'Custom Properties') }}</h2>
		<p class="settings-hint">
			{{ t('customproperties', 'Custom properties defined here are available to all users. They are shown in "Properties" tab in sidebar view. They can be accessed via WebDAV.') }}
		</p>
		<h3>{{ t('customproperties', 'Manage properties') }}</h3>
		<p class="settings-hint">
			{{ t('customproperties', 'Deleting properties will not wipe already set properties. Properties set will be still accessible via WebDAV.') }}
		</p>
		<form @submit="createProperty">
			<div class="form-group">
				<div class="input-group" v-for="(property, index) in properties">
					<div class="form-control-wrapper">
						<label class="form-control-label" :for="'propertylabel_'+property.id">
							<small>
								oc:{{ property.propertyname }}
							</small>
						</label>
						<input class="form-control" :id="'propertylabel_'+property.id" type="text" :name="'propertylabel[' + property.id + ']'" v-model="property.propertylabel">
					</div>
					<button type="button" class="button button-delete" @click="removeProperty(property.id)">{{ t('customproperties', 'Delete') }}</button>
				</div>
				<hr>
				<div class="input-group">
					<input class="form-control form-control__label" type="text" v-model="property.propertylabel" :placeholder="t('customproperties', 'Create property...')">
					<button type="submit" class="button">{{ t('customproperties', 'Save') }}</button>
				</div>
			</div>
		</form>
	</section>
</template>

<script>
	import axios from '@nextcloud/axios'

	export default {
		name: 'AdminSettings',
		components: {
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
				properties: [],
				property: {
					propertylabel: null
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
				const url = OC.generateUrl(`/apps/customproperties/customproperties`);
				const res = await axios.get(url);
				this.properties = res.data;
			},
			async removeProperty(id) {
				const url = OC.generateUrl(`/apps/customproperties/customproperties/${id}`);
				await axios.delete(url);
				await this.getDataFromApi();
			},
			async createProperty(e) {
				e.preventDefault();

				let propertylabel = this.property.propertylabel;
				console.log(propertylabel)

				if(this.isBlank(propertylabel)) {
					OCP.Toast.error(t('customproperties', 'Property has to have a name!'));
					return;
				}

				const url = OC.generateUrl(`/apps/customproperties/customproperties`);
				await axios.post(url, {
					propertylabel
				});
				await this.getDataFromApi();
				this.property.propertylabel = null;
			},
			isBlank(str) {
				return (!str || /^\s*$/.test(str));
			}
		}
	}
</script>
