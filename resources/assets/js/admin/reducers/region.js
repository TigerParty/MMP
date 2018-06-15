import * as actionType  from '../actions/type'

const initState = {
    labels: [],
    list: [],
    isFething: false,
}

function region(state = initState, action) {
    switch (action.type) {
        case actionType.RECEIVE_REGION_LABELS:
            return {
                ...state,
                labels: action.payload
            }
        case actionType.REQUEST_REGION_LIST:
            return {
                ...state,
                isFething: true
            }
        case actionType.RECEIVE_REGION_LIST:
            const { id, payload } = action
            let updateList = payload
            if (id){
                updateList = state.list.map(data => data.id === payload.id ?
                    { ...payload } : data
                )
            }
            return {
                ...state,
                list: updateList,
                isFething: false
            }
        case actionType.REQUEST_PROJECT_LIST_FAIL:
            return {
                ...state,
                list: [],
                isFething: false
            }
        default:
            return state
    }
}

export default region
