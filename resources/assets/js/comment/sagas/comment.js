import { call,
         put,
         all,
         select,
         takeLatest } from 'redux-saga/effects'
import { GET_COMMENT_LIST,
         GET_COMMENT,
         UPDATE_COMMENT_STATUS,
         SUBMIT_COMMENT_CREATE,
         SUBMIT_COMMENT_REPLY,
         DELETE_COMMENT,
         UPDATE_COMMENT_COUNTER } from '../actions/type'
import { updateAuth } from '../actions/auth'
import { requestPageComments,
         receivePageComments,
         requestPageCommentsFail,
         requestCommentCreate,
         receiveCommentCreate,
         requestCommentCreateFail,
         requestCommentReply,
         receiveCommentReply,
         requestCommentReplyFail,
         requestCommentDelete,
         receiveCommentDelete,
         requestCommentDeleteFail } from '../actions/comment'

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

function* fetchPageComments(action) {
    const { id } = action

    yield put(requestPageComments())
    try {
        const result = yield call(fetchApi, `/feedback/comment/${id}/api`)
        yield put(receivePageComments(result.data))
    } catch (error) {
        console.log('Get page comments got error: ', error)
        yield put(requestPageCommentsFail)
    }
}

function* postCommentCreate(action) {
    const { id, entityType } = action
    const commentState = yield select(state => state.comment)
    const postData = {
        new_comment: commentState.newCommentInputs,
        entity_id: id,
        entity_type: entityType
    }

    yield put(requestCommentCreate)
    try {
        const result = yield call(postApi, '/feedback/comment', postData)
        yield put(receiveCommentCreate(result.data, id))
    } catch (error) {
        console.log('Post comment create got error: ', error)
        yield put(requestCommentCreateFail)
    }
}

function* postCommentReply(action) {
    const { id, message } = action
    const postData = {
        message: message
    }

    yield put(requestCommentReply)
    try {
        const result = yield call(postApi, `/feedback/comment/${id}/reply`, postData)
        yield put(receiveCommentReply(result.data, id))
    } catch (error) {
        console.log('Post comment create got error: ', error)
        yield put(requestCommentReplyFail)
    }
}

function* deleteComment(action) {
    const { id } = action

    yield put(requestCommentDelete)
    try {
        const result = yield call(deleteApi, `/feedback/comment/${id}`)
        yield put(receiveCommentDelete(id, result.data))
    } catch (error) {
        console.log('Delete comment got error: ', error)
        yield put(requestCommentDeleteFail)
    }
}

export default function* watchCOMMENT() {
    yield takeLatest(GET_COMMENT, fetchPageComments)
    yield takeLatest(SUBMIT_COMMENT_CREATE, postCommentCreate)
    yield takeLatest(SUBMIT_COMMENT_REPLY, postCommentReply)
    yield takeLatest(DELETE_COMMENT, deleteComment)
}
