import * as actionType from './type'

/*-------- GetProjectList --------*/

export const requestProjectList = ({
    type: actionType.REQUEST_PROJECT_LIST
})

export const receiveProjectList = (projectList, filterOptions, statusList, total, itemsPerPage, projectCountNew) => ({
    type: actionType.RECEIVE_PROJECT_LIST,
    projectList,
    filterOptions,
    statusList,
    total,
    itemsPerPage,
    projectCountNew
})

export const requestProjectListFail = ({
    type: actionType.REQUEST_PROJECT_LIST_FAIL
})

export const getProjectList = (filter) => ({
    type: actionType.GET_PROJECT_LIST,
    filter: filter
})


/*-------- DeleteProject --------*/

export const requestProjectDelete = ({
    type: actionType.REQUEST_PROJECT_DELETE
})

export const receiveProjectDelete = (id) => ({
    type: actionType.RECEIVE_PROJECT_DELETE,
    id
})

export const requestProjectDeleteFail = ({
    type: actionType.REQUEST_PROJECT_DELETE_FAIL
})

export const deleteProject = (id) => ({
    type: actionType.DELETE_PROJECT,
    id
})

/*-------- UpdateProjectStatus --------*/

export const requestProjectStatusUpdate = (id, payload)=> ({
    type: actionType.REQUEST_PROJECT_STATUS_UPDATE,
    id,
    payload
})

export const requestProjectStatusUpdateFail = (id, payload) => ({
    type: actionType.REQUEST_PROJECT_STATUS_UPDATE_FAIL,
    id,
    payload
})

export const updateProjectStatus = (id, payload) => ({
    type: actionType.UPDATE_PROJECT_STATUS,
    id,
    payload
})

/*-------- UpdateProjectApprovel --------*/

export const requestProjectApprovalUpdate = (id, payload)=>({
    type: actionType.REQUEST_PROJECT_APPROVAL_UPDATE,
    id,
    payload
})

export const requestProjectApprovalUpdateFail = (id, payload) => ({
    type: actionType.REQUEST_PROJECT_APPROVAL_UPDATE_FAIL,
    id,
    payload
})

export const updateProjectApproval = (id, payload) => ({
    type: actionType.UPDATE_PROJECT_APPROVAL,
    id,
    payload
})

/*-------- Filter --------*/

export const updateFilter = (type, value, optionalKey) => ({
    type: actionType.UPDATE_FILTER,
    filterType: type,
    payload: value,
    optionalKey
})

export const cleanFilter = () => ({
    type: actionType.CLEAN_FILTER
})


/*-------- Modal --------*/

export const openModal = (type) => ({
    type: actionType.OPEN_MODAL,
    modalType: type
})

export const closeModal = (type) => ({
    type: actionType.CLOSE_MODAL,
    modalType: type
})


