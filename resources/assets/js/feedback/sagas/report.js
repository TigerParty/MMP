import { call,
        put,
        all,
        select,
        takeLatest } from 'redux-saga/effects'
import { GET_REPORT_LIST,
        GET_REPORT,
        UPDATE_REPORT_STATUS,
        DELETE_REPORT } from '../actions/type'
import { updateAuth } from '../actions/auth'
import { requestReportList,
        receiveReportList,
        requestReportListFail,
        requestReport,
        receiveReport,
        requestReportFail,
        requestReportUpdate,
        receiveReportUpdate,
        requestReportUpdateFail,
        requestReportDelete,
        receiveReportDelete,
        requestReportDeleteFail,
        updateReportCounter } from '../actions/report'


function fetchApi(url) {
    return axios.get(url)
}

function updateApi(url, data={}) {
    return axios.put(url, data)
}

function deleteApi(url, data={}) {
    return axios.delete(url, data)
}

function* fetchReportList(action) {
    yield put(requestReportList)
    try {
        const result = yield call(fetchApi, `/feedback/report/api?page=${action.page}`)
        const { report, is_admin, new_amount, total_amount, per_page } = result.data
        yield put(receiveReportList(report, { itemsPerPage: per_page, total: total_amount, current: action.page }))
        yield put(updateAuth(is_admin))
        yield put(updateReportCounter({ new: new_amount, total: total_amount }))
    } catch (error) {
        console.log('Get Report list got error: ', error)
        yield put(requestReportListFail)
    }
}

function* fetchReport(action) {
    const { id } = action
    const reportState = yield select(state => state.report)
    const selectedReport = _.find(reportState.list, {id})

    yield put(requestReport(selectedReport))
    try {
        const result = yield call(fetchApi, `/feedback/report/${id}/api`)
        yield put(receiveReport(result.data))
    } catch (error) {
        console.log('Get Report got error: ', error)
        yield put(requestReportFail)
    }
}

function* updateReportStatus(action) {
    const { id } = action
    const reportState = yield select(state => state.report)
    yield put(requestReportUpdate)
    try {
        const result = yield call(updateApi, `/feedback/report/${id}/mark_read/api`)
        yield put(receiveReportUpdate(id))
        yield put(updateReportCounter({
            ...reportState.counterInfo,
            new: (reportState.counterInfo.new - 1)
        }))
    } catch (error) {
        console.log('Update Report to read got error: ', error)
        yield put(requestReportUpdateFail)
    }
}


function* deleteReport(action) {
    const { id } = action

    yield put(requestReportDelete)
    try {
        const result = yield call(deleteApi, `/feedback/report/${id}/api`)
        yield put(receiveReportDelete(result.data))
    } catch (error) {
        console.log('Delete Report got error: ', error)
        yield put(requestReportDeleteFail)
    }
}

export default function* watchReport() {
    yield takeLatest(GET_REPORT_LIST, fetchReportList)
    yield takeLatest(GET_REPORT, fetchReport)
    yield takeLatest(UPDATE_REPORT_STATUS, updateReportStatus)
    yield takeLatest(DELETE_REPORT, deleteReport)
}
