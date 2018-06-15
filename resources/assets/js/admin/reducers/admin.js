import * as actionType  from '../actions/type'

const initState = {
    projectList: null,
    counter: {
        total: 0,
        new: 0
    },
    filter: {
        page: 0,
        keyword: '',
        regions: [],
        values: {}
    },
    pagination: {
        total: 0,
        from: 0,
        to: 0
    },
    isFetching: false,
    isSubmitting: false,
    isDeleting: false,
    errors: {},
    modals: {
        create: false,
        filter: false,
        batch_entry: false
    },
    region_labels: [],
    filterOptions: [],
    statusList: [],
    oldUpdateData: null
}

function admin(state = initState, action) {
    switch (action.type) {
        case actionType.REQUEST_PROJECT_LIST:
            return {
                ...state,
                isFetching: true
            }
        case actionType.RECEIVE_PROJECT_LIST:
            const { projectList, filterOptions, statusList, itemsPerPage, total, projectCountNew } = action
            const { page } = state.filter
            const from = total > 0 ? (page * itemsPerPage + 1):0
            const to = (page + 1) * itemsPerPage < total && total > 0 ? ((page + 1) * itemsPerPage):total
            return {
                ...state,
                isFetching: false,
                projectList: projectList,
                filterOptions,
                counter: {
                    total: total,
                    new: projectCountNew
                },
                pagination: {
                    ...state.pagination,
                    total,
                    from,
                    to,
                },
                statusList
            }

        case actionType.REQUEST_PROJECT_LIST_FAIL:
            return {
                ...state,
                isFetching: false,
                list: []
            }
        case actionType.REQUEST_PROJECT_DELETE:
            return {
                ...state,
                isDeleting: true
            }
        case actionType.RECEIVE_PROJECT_DELETE:
            let newList = []
            state.projectList.forEach(function (data, index) {
                if (data.id !== action.id) {
                    newList.push(data)
                }
            })
            return {
                ...state,
                isDeleting: false,
                projectList: newList
            }
        case actionType.REQUEST_PROJECT_DELETE_FAIL:
            return {
                ...state,
                isDeleting: false
            }
        case actionType.UPDATE_FILTER:
            const { filterType, payload, optionalKey } = action
            let updateValue = payload
            if (filterType=='regions'){
                const level = optionalKey
                updateValue = [...state.filter.regions]
                if (updateValue.length > level){
                    updateValue[level] = payload
                    updateValue.length = payload.length ? level + 1: level
                }else {
                    updateValue.push(payload)
                }
            }
            return {
                ...state,
                filter: {
                    ...state.filter,
                    page: 0,
                    [filterType]: updateValue
                }
            }
        case actionType.REQUEST_PROJECT_STATUS_UPDATE:
            const { id: updateStatusId, payload: updateStatusData } = action
            return {
                ...state,
                projectList: state.projectList.map(data => data.id === updateStatusId ?
                    { ...data, status_id: updateStatusData } : data
                )
            }
        case actionType.REQUEST_PROJECT_STATUS_UPDATE_FAIL:
            const { id: updateStatusFailId, payload: updateStatusFailData } = action
            return {
                ...state,
                projectList: state.projectList.map(data => data.id === updateStatusFailId ?
                    { ...data, status_id: updateStatusFailData } : data
                )
            }
        case actionType.REQUEST_PROJECT_APPROVAL_UPDATE:
            const { id: updateApprovalId, payload: updateApprovalData } = action
            return {
                ...state,
                projectList: state.projectList.map(data => data.id === updateApprovalId ?
                    { ...data, is_approved: updateApprovalData } : data
                )
            }
        case actionType.REQUEST_PROJECT_APPROVAL_UPDATE_FAIL:
            const { id: updateApprovalFailId, payload: updateApprovalFailData } = action
            return {
                ...state,
                projectList: state.projectList.map(data => data.id === updateApprovalFailId ?
                    { ...data, is_approved: updateApprovalFailData } : data
                )
            }
        case actionType.CLEAN_FILTER:
            return {
                ...state,
                filter: {
                    page: 1,
                    keyword: ''
                }
            }
        case actionType.OPEN_MODAL:
            const modalOpenType = action.modalType
            return {
                ...state,
                modals: {
                    ...state.modals,
                    [modalOpenType]: true
                }
            }
        case actionType.CLOSE_MODAL:
            const modalCloseType = action.modalType
            return {
                ...state,
                modals: {
                    ...state.modals,
                    [modalCloseType]: false,
                }
            }
        default:
            return state
    }
}

export default admin
