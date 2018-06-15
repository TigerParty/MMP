import { call,
         put,
         all,
         select,
         takeLatest } from 'redux-saga/effects'
import { GET_VOICE_LIST,
         GET_VOICE,
         UPDATE_VOICE_STATUS,
         DELETE_VOICE } from '../actions/type'
import { requestVoiceList,
         receiveVoiceList,
         requestVoiceListFail,
         requestVoice,
         receiveVoice,
         requestVoiceFail,
         requestVoiceUpdate,
         receiveVoiceUpdate,
         requestVoiceUpdateFail,
         updateVoiceCounter } from '../actions/voice'


function fetchApi(url) {
    return axios.get(url)
}

function updateApi(url, data={}) {
    return axios.put(url, data)
}

function* fetchVoiceList() {
    yield put(requestVoiceList)
    try {
        const result = yield call(fetchApi, '/feedback/voice/api')
        yield put(receiveVoiceList(result.data.payload))
        yield put(updateVoiceCounter)
    } catch (error) {
        console.log('Get Voice list got error: ', error)
        yield put(requestVoiceListFail)
    }
}

function* fetchVoice(action) {
    const { id } = action
    const voiceState = yield select(state => state.voice)
    const selectedVoice = _.find(voiceState.list, {id})

    yield put(requestVoice(selectedVoice))
    try {
        const result = yield call(fetchApi, `/feedback/voice/${id}/api`)
        yield put(receiveVoice(result.data.payload))
    } catch (error) {
        console.log('Get Voice got error: ', error)
        yield put(requestVoiceFail)
    }
}

function* updateVoiceStatus(action) {
    const { id } = action

    yield put(requestVoiceUpdate)
    try {
        const result = yield call(updateApi, `/feedback/voice/${id}/mark_read/api`)
        yield put(receiveVoiceUpdate(id))
        yield put(updateVoiceCounter)
    } catch (error) {
        console.log('Update Voice to read got error: ', error)
        yield put(requestVoiceUpdateFail)
    }
}


export default function* watchVoice() {
    yield takeLatest(GET_VOICE_LIST, fetchVoiceList)
    yield takeLatest(GET_VOICE, fetchVoice)
    yield takeLatest(UPDATE_VOICE_STATUS, updateVoiceStatus)
}
