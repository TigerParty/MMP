import { call,
         put,
         all,
         select,
         takeLatest } from 'redux-saga/effects'
import { GET_USER_LIST,
         GET_USER,
         CREATE_USER,
         UPDATE_USER,
         DELETE_USER,
         UPDATE_USER_NOTIFY,
         GET_PROJECT_LIST } from '../actions/type'
import { updateAuth } from '../actions/auth'
import { requestUserList,
         receiveUserList,
         requestUserListFail,
         requestUserCreate,
         receiveUserCreateSuccess,
         requestUserCreateFail,
         requestUserUpdate,
         receiveUserUpdateSuccess,
         requestUserUpdateFail,
         requestUserDelete,
         receiveUserDelete,
         requestUserDeleteFail,
         requestUserNotifyUpdate,
         requestUserNotifyUpdateFail,
         requestUser,
         receiveUser,
         requestUserFail,
         requestProjectList,
         receiveProjectList,
         requestProjectListFail, } from '../actions/user'


function fetchApi(url) {
    return axios.get(url)
}

function updateApi(url, data={}) {
    return axios.put(url, data)
}

function postApi(url, data={}) {
    return axios.post(url, data)
}

function deleteApi(url, data={}) {
    return axios.delete(url, data)
}

function* fetchUserList(action) {
    const { filter } = action
    yield put(requestUserList)
    try {
        const result = yield call(postApi, '/management/user/query', arrangeFilterData(filter))
        yield put(receiveUserList(result.data.users,
            result.data.permissions,
            result.data.items_per_page,
            result.data.user_count))
    } catch (error) {
        console.log('Get user list got error: ', error)
        yield put(requestUserListFail)
    }
}

function arrangeFilterData(filter) {
    let filterObj = { page: filter.page}
    if(filter.keyword.length){
        filterObj['keyword'] = filter.keyword
    }
    if(filter.permission_level_id.length){
        filterObj['permission_level_id'] = filter.permission_level_id
    }
    return filterObj
}

function* createUser(action) {
    const { payload } = action
    yield put(requestUserCreate)
    try {
        const result = yield call(postApi, '/management/user', payload)
        yield put(receiveUserCreateSuccess)
    } catch (error) {
        console.log('Create User got error: ', error)
        let errorPayload
        if(error.response.status == '422'){
            errorPayload = error.response.data.errors
        }else {
            errorPayload = { public: "Create User got error" }
        }
        yield put(requestUserCreateFail(errorPayload))
    }
}

function* updateUser(action) {
    const { payload, id } = action
    yield put(requestUserUpdate)
    try {
        let updateData = {}
        _.keys(payload).map(function(key) {
            const val = payload[key]
            if (val && val.length > 0 || val && key == "project_ids"){
                _.set(updateData, key, payload[key])
            }
        })
        const result = yield call(updateApi, `/management/user/${id}`, updateData)
        yield put(receiveUserUpdateSuccess)
    } catch (error) {
        console.log('Update User got error: ', error)
        let errorPayload
        if(Object.prototype.hasOwnProperty.call(error, 'response') &&
            error.response.status == '422'){
            errorPayload = error.response.data.errors
        }else {
            errorPayload = { public: "Update User got error" }
        }
        yield put(requestUserUpdateFail(errorPayload))
    }
}

function* updateUserNotify(action) {
    const { payload, id } = action
    yield put(requestUserNotifyUpdate(payload, id))
    try {
        yield call(postApi, `/management/user/${id}/switch_notify`, { notification_enabled: +payload})
    }catch (error) {
        console.log('Update User Notify got error: ', error)
        yield put(requestUserNotifyUpdateFail(payload, id))
    }
}

function* deleteUser(action) {
    const { id } = action
    yield put(requestUserDelete)
    try {
        const result = yield call(deleteApi, `/management/user/${id}`)
        yield put(receiveUserDelete(id))
    } catch (error) {
        console.log('Delete User got error: ', error)
        yield put(requestUserDeleteFail)
    }
}

function* getUser(action) {
    const { id } = action
    yield put(requestUser)
    try {
        const result = yield call(fetchApi, `/management/user/${id}`)
        yield put(receiveUser(result.data.user))
    } catch (error) {
        console.log('Get User got error: ', error)
        yield put(requestUserFail)
    }
}

function* getProjectList(action) {
    const { payload } = action
    yield put(requestProjectList)
    try {
        const result = yield call(postApi, '/management/project/query', { keyword: payload})
        yield put(receiveProjectList(result.data.project))
    } catch (error) {
        console.log('Get Project List got error: ', error)
        yield put(requestProjectListFail)
    }
}

export default function* watchUser() {
    yield takeLatest(GET_USER_LIST, fetchUserList)
    yield takeLatest(CREATE_USER, createUser)
    yield takeLatest(UPDATE_USER, updateUser)
    yield takeLatest(DELETE_USER, deleteUser)
    yield takeLatest(UPDATE_USER_NOTIFY, updateUserNotify),
    yield takeLatest(GET_USER, getUser),
    yield takeLatest(GET_PROJECT_LIST, getProjectList)
}
