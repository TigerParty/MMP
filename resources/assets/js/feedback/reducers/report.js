import * as actionType  from '../actions/type'
import { counterCalculate } from './index'


const initState = {
    list: [],
    report: null,
    isFetching: false,
    fetchError: false,
    isReadUpdating: false,
    selectedReport: null,
    counterInfo: {
        new: 0,
        total: 0
    },
    pagination: {
        current: 1,
        from: 0,
        to: 0,
        total: 0
    },
    isDeleting: false
}

function report(state = initState, action) {
    switch (action.type) {
        case actionType.REQUEST_REPORT_LIST:
            return {
                ...state,
                isFetching: true
            }
        case actionType.RECEIVE_REPORT_LIST:
            const reportList = action.payload
            const { itemsPerPage, current, total } = action.pagination
            const from = total > 0 ? ((current - 1) * itemsPerPage + 1) : 0
            const to = current * itemsPerPage < total && total > 0 ? (current * itemsPerPage) : total
            return {
                ...state,
                isFetching: false,
                list: reportList,
                pagination: {
                    current,
                    from,
                    to,
                    total
                }
            }
        case actionType.REQUEST_REPORT_LIST_FAIL:
            return {
                ...state,
                isFetching: false,
                list: []
            }
        case actionType.REQUEST_REPORT:
            return {
                ...state,
                isFetching: true,
                selectedReport: action.report,
                report: null,
                fetchError: false
            }
        case actionType.RECEIVE_REPORT:
            return {
                ...state,
                isFetching: false,
                report: action.payload
            }
        case actionType.REQUEST_REPORT_FAIL:
            return {
                ...state,
                isFetching: false,
                report: null,
                fetchError: true
            }
        case actionType.REQUEST_REPORT_UPDATE:
            return {
                ...state,
                isReadUpdating: true
            }
        case actionType.RECEIVE_REPORT_UPDATE:
            return {
                ...state,
                isReadUpdating: false,
                list: state.list.map(data => data.id === action.id ?
                    { ...data, is_read: 1 } : data
                )
            }
        case actionType.REQUEST_REPORT_DELETE:
            return {
                ...state,
                isDeleting: true,
            }
        case actionType.RECEIVE_REPORT_DELETE:
            return {
                ...state,
                isDeleting: false,
                selectedReport: null,
                report: null
            }
        case actionType.REQUEST_REPORT_DELETE_FAIL:
            return {
                ...state,
                isDeleting: false
            }
        case actionType.UPDATE_REPORT_COUNTER:
            return {
                ...state,
                counterInfo: {
                    new: action.payload.new,
                    total: action.payload.total,
                }
            }
        default:
            return state
    }
}


export default report
