import * as actionType  from '../actions/type'
import { counterCalculate } from './index'


const initState = {
    list: [],
    messages: [],
    isFetching: false,
    fetchError: false,
    isReadUpdating: false,
    selectedVoice: null,
    counterInfo: {
        new: 0,
        total: 0
    },
    isDeleting: false
}

function voice(state = initState, action) {
    switch (action.type) {
        case actionType.REQUEST_VOICE_LIST:
            return {
                ...state,
                isFetching: true
            }
        case actionType.RECEIVE_VOICE_LIST:
            const voiceList = action.payload
            return {
                ...state,
                isFetching: false,
                list: voiceList,
            }
        case actionType.REQUEST_VOICE_LIST_FAIL:
            return {
                ...state,
                isFetching: false,
                list: []
            }
        case actionType.REQUEST_VOICE:
            return {
                ...state,
                isFetching: true,
                selectedVoice: action.voice,
                messages: [],
                fetchError: false
            }
        case actionType.RECEIVE_VOICE:
            return {
                ...state,
                isFetching: false,
                messages: action.payload
            }
        case actionType.REQUEST_VOICE_FAIL:
            return {
                ...state,
                isFetching: false,
                messages: [],
                fetchError: true
            }
        case actionType.REQUEST_VOICE_UPDATE:
            return {
                ...state,
                isReadUpdating: true
            }
        case actionType.RECEIVE_VOICE_UPDATE:
            return {
                ...state,
                isReadUpdating: false,
                list: state.list.map(data => data.id === action.id ?
                    { ...data, is_read: 1 } : data
                )
            }
        case actionType.UPDATE_VOICE_COUNTER:
            const counterInfo = counterCalculate(state.list, true)
            return {
                ...state,
                counterInfo
            }
        default:
            return state
    }
}

export default voice
