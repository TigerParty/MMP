import * as actionType  from '../actions/type'

const initState = {
    list: null,
    filter: {
      page: 1,
      keyword: '',
      permission_level_id: ''},
    pagination: {
      total: 0,
      from: 0,
      to: 0
    },
    isFetching: false,
    isSubmitting: false,
    isDeleting: false,
    errors: {},
    permissions: [],
    modals: {
        create: false,
        update: false,
        userRoles: false,
        addProject: false
    },
    selectedUser: null,
    projectFilter: '',
    projectList: []
}

function user(state = initState, action) {
    switch (action.type) {
        case actionType.REQUEST_USER_LIST:
            return {
                ...state,
                isFetching: true
            }
        case actionType.RECEIVE_USER_LIST:
            const { userList, permissionList, itemsPerPage, total } = action
            const { page } = state.filter
            const from = total > 0 ? ((page-1) * itemsPerPage + 1):0
            const to = page*itemsPerPage < total && total > 0 ? (page * itemsPerPage):total
            return {
                ...state,
                isFetching: false,
                list: userList,
                permissions: permissionList,
                pagination: {
                    ...state.pagination,
                    total,
                    from,
                    to,
                }
            }

        case actionType.REQUEST_USER_LIST_FAIL:
            return {
                ...state,
                isFetching: false,
                list: [],
                permissions: []
            }
        case actionType.UPDATE_FILTER:
            const type = action.filterType
            const value = action.payload
            return {
                ...state,
                filter: {
                    ...state.filter,
                    page: 1,
                    [type]: value
                }
            }
        case actionType.CLEAN_FILTER:
            return {
                ...state,
                filter: {
                    page: 1,
                    keyword: '',
                    permission_level_id: ''
                }
            }
        case actionType.REQUEST_USER_CREATE:
            return {
                ...state,
                isSubmitting: true,
                errors: {}
            }
        case actionType.RECEIVE_USER_CREATE_SUCCESS:
            return {
                ...state,
                isSubmitting: false
            }
        case actionType.REQUEST_USER_CREATE_FAIL:
            return {
                ...state,
                isSubmitting: false,
                errors: action.errors
            }
        case actionType.REQUEST_USER_UPDATE:
            return {
                ...state,
                isSubmitting: true,
                errors: {}
            }
        case actionType.RECEIVE_USER_UPDATE_SUCCESS:
            return {
                ...state,
                isSubmitting: false
            }
        case actionType.REQUEST_USER_UPDATE_FAIL:
            return {
                ...state,
                isSubmitting: false,
                errors: action.errors
            }
        case actionType.REQUEST_USER_DELETE:
            return {
                ...state,
                isDeleting: true
            }
        case actionType.RECEIVE_USER_DELETE:
            let newList = []
            state.list.forEach(function(data, index) {
                if(data.id !== action.id) {
                    newList.push(data)
                }
            })
            return {
                ...state,
                isDeleting: false,
                list: newList
            }
        case actionType.REQUEST_USER_DELETE_FAIL:
            return {
                ...state,
                isDeleting: false
            }
        case actionType.OPEN_USER_MODAL:
            const modalOpenType = action.modalType
            const selectedId = action.selectedId
            return {
                ...state,
                selectedUser: selectedId? _.find(state.list, {id: selectedId}):null,
                modals: {
                    ...state.modals,
                    [modalOpenType]: true
                }
            }
        case actionType.CLOSE_USER_MODAL:
            const modalCloseType = action.modalType
            return {
                ...state,
                errors: {},
                selectedUser: null,
                modals: {
                    ...state.modals,
                    [modalCloseType]: false,
                }
            }
        case actionType.REQUEST_USER_NOTIFY_UPDATE:
            const { id: updateId, payload: updateData } = action
            return {
                ...state,
                list: state.list.map(data => data.id === updateId ?
                    { ...data, notification_enabled: updateData } : data
                )
            }
        case actionType.REQUEST_USER_NOTIFY_UPDATE_FAIL:
            const { id: updateFailId, payload: updateFailData } = action
            return {
                ...state,
                list: state.list.map(data => data.id === updateFailId ?
                    { ...data, notification_enabled: updateFailData } : data
                )
            }
        case actionType.REQUEST_USER:
            return {
                ...state,
                selectedUser: null,
                isFetching: true
            }
        case actionType.RECEIVE_USER:
            return {
                ...state,
                selectedUser: action.payload,
                isFetching: false,
            }
        case actionType.REQUEST_USER_FAIL:
            return {
                ...state,
                selectedUser: null,
                isFetching: false
            }
        case actionType.UPDATE_PROJECT_FILTER:
            return {
                ...state,
                projectFilter: action.payload
            }
        case actionType.CLEAN_PROJECT_FILTER:
            return {
                ...state,
                projectFilter: ''
            }
        case actionType.REQUEST_PROJECT_LIST:
            return {
                ...state,
                isFetching: true
            }
        case actionType.RECEIVE_PROJECT_LIST:
            return {
                ...state,
                isFetching: false,
                projectList: action.payload
            }

        case actionType.REQUEST_PROJECT_LIST_FAIL:
            return {
                ...state,
                isFetching: false,
                projectList: [],
                projectFilter: ''
            }
        default:
            return state
    }
}

export default user
