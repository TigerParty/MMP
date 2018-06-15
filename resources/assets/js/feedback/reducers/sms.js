import * as actionType  from '../actions/type'
import { MESSAGE_HOST_TYPE } from '../constants'
import { counterCalculate } from './index'

const initState = {
    list: [],
    messages: [],
    isFetching: false,
    fetchError: false,
    isReadUpdating: false,
    selectedSms: null,
    counterInfo: {
        new: 0,
        total: 0,
        unresponded: 0,
        responded: 0
    },
    pagination: {
        current: 1,
        from: 0,
        to: 0,
        total: 0
    },
    replyMessage: null,
    isReplying: false,
    isDeleting: false
}

function sms(state = initState, action) {
    switch (action.type) {
        case actionType.REQUEST_SMS_LIST:
            return {
                ...state,
                isFetching: true
            }
        case actionType.RECEIVE_SMS_LIST:
            const smsList = action.payload
            const { itemsPerPage, current, total  } = action.pagination
            const from = total > 0 ? ((current - 1) * itemsPerPage + 1) : 0
            const to = current * itemsPerPage < total && total > 0 ? (current * itemsPerPage) : total
            return {
                ...state,
                isFetching: false,
                list: smsList,
                pagination: {
                    current,
                    from,
                    to,
                    total
                }
            }
        case actionType.REQUEST_SMS_LIST_FAIL:
            return {
                ...state,
                isFetching: false,
                list: []
            }
        case actionType.REQUEST_SMS:
            return {
                ...state,
                isFetching: true,
                selectedSms: action.sms,
                messages: [],
                fetchError: false
            }
        case actionType.RECEIVE_SMS:
            return {
                ...state,
                isFetching: false,
                messages: action.payload
            }
        case actionType.REQUEST_SMS_FAIL:
            return {
                ...state,
                isFetching: false,
                messages: [],
                fetchError: true
            }
        case actionType.REQUEST_SMS_UPDATE:
            return {
                ...state,
                isReadUpdating: true
            }
        case actionType.RECEIVE_SMS_UPDATE:
            return {
                ...state,
                isReadUpdating: false,
                list: state.list.map(data => data.id === action.id ?
                    { ...data, is_read: 1 } : data
                )
            }
        case actionType.REQUEST_SMS_UPDATE_FAIL:
            return {
                ...state,
                isReadUpdating: false
            }
        case actionType.INPUT_SMS_REPLY:
            return {
                ...state,
                replyMessage: action.value
            }
        case actionType.REQUEST_SMS_REPLY:
            return {
                ...state,
                isReplying: true,
            }
        case actionType.RECEIVE_SMS_REPLY:
            let newRely = { type: MESSAGE_HOST_TYPE, ...action.payload }
            return {
                ...state,
                isReplying: false,
                replyMessage: null,
                messages: [...state.messages, newRely],
                list: state.list.map(data => data.id === action.id ?
                    { ...data } : data
                )
            }
        case actionType.REQUEST_SMS_REPLY_FAIL:
            return {
                ...state,
                isReplying: false
            }
        case actionType.REQUEST_SMS_DELETE:
            return {
                ...state,
                isDeleting: true,
            }
        case actionType.RECEIVE_SMS_DELETE:
            return {
                ...state,
                isDeleting: false,
                selectedSms: null,
                messages: []
            }
        case actionType.REQUEST_SMS_DELETE_FAIL:
            return {
                ...state,
                isDeleting: false
            }
        case actionType.UPDATE_SMS_COUNTER:
            return {
                ...state,
                counterInfo: {
                    new: action.payload.new,
                    total: action.payload.total,
                    unresponded: (action.payload.total - action.payload.responded),
                    responded: action.payload.responded,
                }
            }
        default:
            return state
    }
}

export default sms
