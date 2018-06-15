import { call,
        put,
        all,
        select,
        takeLatest } from 'redux-saga/effects'
import { GET_SMS_LIST,
        GET_SMS,
        UPDATE_SMS_STATUS,
        SUBMIT_SMS_REPLY,
        DELETE_SMS,
        UPDATE_SMS_COUNTER } from '../actions/type'
import { updateAuth } from '../actions/auth'
import { requestSmsList,
        receiveSmsList,
        requestSmsListFail,
        requestSms,
        receiveSms,
        requestSmsFail,
        requestSmsUpdate,
        receiveSmsUpdate,
        requestSmsUpdateFail,
        requestSmsReply,
        receiveSmsReply,
        requestSmsReplyFail,
        requestSmsDelete,
        receiveSmsDelete,
        requestSmsDeleteFail,
        updateSmsCounter } from '../actions/sms'


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

function* fetchSmsList(action) {
    yield put(requestSmsList)
    try {
        const result = yield call(fetchApi, `/feedback/sms/api?page=${action.page}`)
        const { sms, is_admin, new_amount, responded_amount, total_amount, per_page } = result.data
        yield put(receiveSmsList(sms, { itemsPerPage: per_page, total: total_amount, current: action.page }))
        yield put(updateAuth(is_admin))
        yield put(updateSmsCounter({ new: new_amount, responded: responded_amount, total: total_amount }))
    } catch (error) {
        console.log('Get sms list got error: ', error)
        yield put(requestSmsListFail)
    }
}

function* fetchSms(action) {
    const { id } = action
    const smsState = yield select(state => state.sms)
    const selectedSms = _.find(smsState.list, {id})
    if(!selectedSms) {
        yield put(requestSmsFail)
        return
    }

    yield put(requestSms(selectedSms))
    try {
        const result = yield call(fetchApi, `/feedback/sms/${id}/api`)
        yield put(receiveSms(result.data))
    } catch (error) {
        console.log('Get sms got error: ', error)
        yield put(requestSmsFail)
    }
}

function* updateSmsStatus(action) {
    const { id } = action
    const smsState = yield select(state => state.sms)

    yield put(requestSmsUpdate)
    try {
        const result = yield call(updateApi, `/feedback/sms/${id}/mark_read/api`)
        yield put(receiveSmsUpdate(id))
        yield put(updateSmsCounter(
            {...smsState.counterInfo,
                new: (smsState.counterInfo.new - 1)}))
    } catch (error) {
        console.log('Update sms to read got error: ', error)
        yield put(requestSmsUpdateFail)
    }
}

function* postSmsReplyMessage(action) {
    const { id } = action
    const smsState = yield select(state => state.sms)
    const postData = {
        citizen_sms_id: id,
        message: smsState.replyMessage
    }

    yield put(requestSmsReply)
    try {
        const result = yield call(postApi, '/feedback/sms_reply', postData)
        yield put(receiveSmsReply(result.data, id))
        yield put(updateSmsCounter(
            {
                ...smsState.counterInfo,
                responded: (smsState.counterInfo.responded + 1)
            }))
    } catch (error) {
        console.log('Post sms reply got error: ', error)
        yield put(requestSmsReplyFail)
    }
}

function* deleteSms(action) {
    const { id } = action
    const smsState = yield select(state => state.sms)
    const selectedSms = _.find(smsState.list, {id})

    yield put(requestSmsDelete)
    try {
        const result = yield call(deleteApi, `/feedback/sms/${id}/api`)
        yield put(receiveSmsDelete(result.data))
    } catch (error) {
        console.log('Delete Sms got error: ', error)
        yield put(requestSmsDeleteFail)
    }
}

export default function* watchSMS() {
    yield takeLatest(GET_SMS_LIST, fetchSmsList)
    yield takeLatest(GET_SMS, fetchSms)
    yield takeLatest(UPDATE_SMS_STATUS, updateSmsStatus)
    yield takeLatest(SUBMIT_SMS_REPLY, postSmsReplyMessage)
    yield takeLatest(DELETE_SMS, deleteSms)
}
