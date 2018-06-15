import {
    call,
    put,
    all,
    select,
    takeLatest
} from 'redux-saga/effects'
import { GET_PROJECT_LIST, DELETE_PROJECT, UPDATE_PROJECT_STATUS, UPDATE_PROJECT_APPROVAL, REQUEST_PROJECT_APPROVAL_UPDATE_FAIL } from '../actions/type'
import { updateAuth } from '../actions/auth'
import {
    requestProjectList,
    receiveProjectList,
    requestProjectListFail,
    requestProjectDelete,
    receiveProjectDelete,
    requestProjectDeleteFail,
    requestProjectStatusUpdate,
    requestProjectStatusUpdateFail,
    requestProjectApprovalUpdate,
    requestProjectApprovalUpdateFail } from '../actions/admin'
import { receiveRegionLabels } from '../actions/region'


function fetchApi(url) {
    return axios.get(url)
}

function updateApi(url, data = {}) {
    return axios.put(url, data)
}

function postApi(url, data = {}) {
    return axios.post(url, data)
}

function deleteApi(url, data = {}) {
    return axios.delete(url, data)
}

function* fetchProjectList(action) {
    const { filter } = action
    yield put(requestProjectList)
    try {
        const result = yield call(postApi, '/admin/project/query', arrangeFilterData(filter))
        yield put(receiveProjectList(result.data.project,
            result.data.filter,
            result.data.status,
            result.data.project_count,
            result.data.items_per_page,
            result.data.project_count_new))
        yield put(receiveRegionLabels(result.data.region_label))
    } catch (error) {
        console.log('Get project list got error: ', error)
        yield put(requestProjectListFail)
    }
}

function arrangeFilterData(filter) {
    let filterObj = { page: filter.page }
    if (filter.keyword.length) {
        filterObj['keyword'] = filter.keyword
    }
    if (filter.regions.length) {
        filterObj['region_ids'] = filter.regions
    }
    if (!_.isEmpty(filter.values)) {
        filterObj['values'] = filter.values
    }
    return filterObj
}

function* deleteProject(action) {
    const { id } = action
    yield put(requestProjectDelete)
    try {
        const result = yield call(deleteApi, `/admin/project/${id}`)
        yield put(receiveProjectDelete(id))
    } catch (error) {
        console.log('Delete Project got error: ', error)
        yield put(requestProjectDeleteFail)
    }
}

function* updateProjectStatus(action) {
    const { payload, id } = action
    const adminState = yield select(state => state.admin)
    const projectItem = _.find(adminState.projectList, ['id', id])
    const oldItemStatusData = projectItem.status_id
    yield put(requestProjectStatusUpdate(id, payload))
    try {
        yield call(postApi, `/admin/project/${id}/status`, { status_id: payload })
    } catch (error) {
        console.log('Update Project Status got error: ', error)
        yield put(requestProjectStatusUpdateFail(id, oldItemStatusData))
    }
}

function* updateProjectApproval(action) {
    const { payload, id } = action
    const adminState = yield select(state => state.admin)
    const projectItem = _.find(adminState.projectList, ['id', id])
    const oldItemApprovalData = projectItem.is_approved
    yield put(requestProjectApprovalUpdate(id, payload))
    try {
        yield call(updateApi, `/admin/project/${id}/approval`, { approved: payload })
    } catch (error) {
        console.log('Update Project Approval got error: ', error)
        yield put(requestProjectApprovalUpdateFail(id, oldItemApprovalData))
    }
}


export default function* watchAdmin() {
    yield takeLatest(GET_PROJECT_LIST, fetchProjectList)
    yield takeLatest(DELETE_PROJECT, deleteProject)
    yield takeLatest(UPDATE_PROJECT_STATUS, updateProjectStatus)
    yield takeLatest(UPDATE_PROJECT_APPROVAL, updateProjectApproval)
}