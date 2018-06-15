import * as actionType from './type'

/*-------- GetUserList --------*/

export const requestUserList = ({
    type: actionType.REQUEST_USER_LIST
})

export const receiveUserList = (userList, permissionList, itemsPerPage, total) => ({
    type: actionType.RECEIVE_USER_LIST,
    userList,
    permissionList,
    itemsPerPage,
    total
})

export const requestUserListFail = ({
    type: actionType.REQUEST_USER_LIST_FAIL
})

export const getUserList = (filter) => ({
    type: actionType.GET_USER_LIST,
    filter: filter
})


/*-------- CreateUser --------*/

export const requestUserCreate = ({
    type: actionType.REQUEST_USER_CREATE
})

export const receiveUserCreateSuccess = ({
    type: actionType.RECEIVE_USER_CREATE_SUCCESS
})

export const requestUserCreateFail = (errors) =>({
    type: actionType.REQUEST_USER_CREATE_FAIL,
    errors
})

export const createUser = (data) => ({
    type: actionType.CREATE_USER,
    payload: data
})

/*-------- UpdateUser --------*/

export const requestUserUpdate = ({
    type: actionType.REQUEST_USER_UPDATE
})

export const receiveUserUpdateSuccess = ({
    type: actionType.RECEIVE_USER_UPDATE_SUCCESS
})

export const requestUserUpdateFail = (errors) =>({
    type: actionType.REQUEST_USER_UPDATE_FAIL,
    errors
})

export const updateUser = (data, id) => ({
    type: actionType.UPDATE_USER,
    payload: data,
    id
})


/*-------- UpdateUserNotfity --------*/

export const requestUserNotifyUpdate = (data, id)=>({
    type: actionType.REQUEST_USER_NOTIFY_UPDATE,
    id,
    payload: data
})

export const requestUserNotifyUpdateFail = (data, id) =>({
    type: actionType.REQUEST_USER_NOTIFY_UPDATE_FAIL,
    payload: !data,
    id
})

export const updateUserNotify = (data, id) => ({
    type: actionType.UPDATE_USER_NOTIFY,
    payload: data,
    id
})


/*-------- DeleteUser --------*/

export const requestUserDelete = ({
    type: actionType.REQUEST_USER_DELETE
})

export const receiveUserDelete = (id) =>({
    type: actionType.RECEIVE_USER_DELETE,
    id
})

export const requestUserDeleteFail = ({
    type: actionType.REQUEST_USER_DELETE_FAIL
})

export const deleteUser = (id) => ({
    type: actionType.DELETE_USER,
    id
})

/*-------- GetUser --------*/

export const requestUser = ({
    type: actionType.REQUEST_USER
})

export const receiveUser = (data)=>({
    type: actionType.RECEIVE_USER,
    payload: data
})

export const requestUserFail = ({
    type: actionType.REQUEST_USER_FAIL,
})

export const getUser = (id) => ({
    type: actionType.GET_USER,
    id
})


/*-------- Filter --------*/

export const updateFilter = (type, value) => ({
    type: actionType.UPDATE_FILTER,
    filterType: type,
    payload: value
})

export const cleanFilter = () => ({
    type: actionType.CLEAN_FILTER
})

export const updateProjectFilter = (data) => ({
    type: actionType.UPDATE_PROJECT_FILTER,
    payload: data
})

export const cleanProjectFilter = () => ({
    type: actionType.CLEAN_PROJECT_FILTER
})


/*-------- Modal --------*/

export const openModal = (type, selectedId) => ({
    type: actionType.OPEN_USER_MODAL,
    modalType: type,
    selectedId
})

export const closeModal = (type) => ({
    type: actionType.CLOSE_USER_MODAL,
    modalType: type
})


/*-------- ProjectList --------*/

export const requestProjectList = ({
    type: actionType.REQUEST_PROJECT_LIST
})

export const receiveProjectList = (data) => ({
    type: actionType.RECEIVE_PROJECT_LIST,
    payload: data
})

export const requestProjectListFail = ({
    type: actionType.REQUEST_PROJECT_LIST_FAIL
})

export const getProjectList = (filter) => ({
    type: actionType.GET_PROJECT_LIST,
    payload: filter
})
