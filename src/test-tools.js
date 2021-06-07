import { render as vueRender } from '@testing-library/vue'
import { translate } from '@nextcloud/l10n'

export const render = async(component, options = {}) => await vueRender(component, options, (Vue) => {
	Vue.prototype.t = translate
})
