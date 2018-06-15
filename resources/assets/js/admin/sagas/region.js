import {
    call,
    put,
    all,
    select,
    takeLatest
} from 'redux-saga/effects'
import { GET_REGION_LIST } from '../actions/type'
import {
    requestRegionList,
    receiveRegionList,
    requestRegionListFail } from '../actions/region'


function fetchApi(url) {
    return axios.get(url)
}

function postApi(url, data = {}) {
    return axios.post(url, data)
}

function* fetchRegionList(action) {
    const { id } = action
    yield put(requestRegionList)
    try {
        const FETCH_URL = id ? `/region/${id}/api` : '/region/api'
        const result = yield call(fetchApi, FETCH_URL)
        yield put(receiveRegionList(result.data, id))
    } catch (error) {
        console.log('Get region list got error: ', error)
        yield put(requestRegionListFail)
    }
}

export default function* watchRegion() {
    yield takeLatest(GET_REGION_LIST, fetchRegionList)
}