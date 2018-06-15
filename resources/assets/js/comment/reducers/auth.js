import * as actionType  from '../actions/type'

const initState = {
    isAdmin: false
}

function auth(state = initState, action) {
    switch (action.type) {
        case actionType.UPDATE_AUTH:
            return {
                ...state,
                isAdmin: action.payload
            }
        default:
            return state
    }
}

export default auth
