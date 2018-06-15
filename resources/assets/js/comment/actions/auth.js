import * as actionType from './type'

export const updateAuth = (isLogin) => ({
    type: actionType.UPDATE_AUTH,
    payload: isLogin
})
