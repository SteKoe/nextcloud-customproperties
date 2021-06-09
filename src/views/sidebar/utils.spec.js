import { isEmptyObject } from './utils'

describe('utils.js', () => {
	test.each`
		obj
		${{}}
		${null}
		${undefined}
	`('"$obj" should be identified as empty object', ({ obj }) => {
		expect(isEmptyObject(obj)).toBeTruthy()
	})
})
