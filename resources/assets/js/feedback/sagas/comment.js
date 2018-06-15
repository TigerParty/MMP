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
import { requestCommentList,
        receiveCommentList,
        requestCommentListFail,
        requestPageComments,
        receivePageComments,
        requestPageCommentsFail,
        requestCommentUpdate,
        receiveCommentUpdate,
        requestCommentUpdateFail,
        requestCommentCreate,
        receiveCommentCreate,
        requestCommentCreateFail,
        requestCommentReply,
        receiveCommentReply,
        requestCommentReplyFail,
        requestCommentDelete,
        receiveCommentDelete,
        requestCommentDeleteFail,
        updateCommentCounter } from '../actions/comment'

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

function* fetchCommentList(action) {
    yield put(requestCommentList)
    try {
        const result = yield call(fetchApi, `/feedback/comment/api?page=${action.page}`)
        const { list, is_admin, new_amount, responded_amount, total_amount, per_page } = result.data
        yield put(receiveCommentList(list, { itemsPerPage: per_page, total: list.length, current: action.page }))
        yield put(updateAuth(is_admin))
        yield put(updateCommentCounter({ new: new_amount, responded: responded_amount, total: total_amount }))
    } catch (error) {
        console.log('Get comment list got error: ', error)
        yield put(requestCommentListFail)
    }
}

function* fetchPageComments(action) {
    const { id } = action
    const commentState = yield select(state => state.comment)
    const selectedPageComment = _.find(commentState.list, {id})
    if(!selectedPageComment) {
        yield put(requestPageCommentsFail)
        return
    }

    yield put(requestPageComments(selectedPageComment))
    try {
        const result = yield call(fetchApi, `/feedback/comment/${id}/api`)
        yield put(receivePageComments(result.data))
    } catch (error) {
        console.log('Get page comments got error: ', error)
        yield put(requestPageCommentsFail)
    }
}

function* updateCommentStatus(action) {
    const { id } = action
    const commentState = yield select(state => state.comment)
    console.log(commentState.selectedPageComment)

    yield put(requestCommentUpdate)
    try {
        const result = yield call(updateApi, `/feedback/comment/${id}/mark_read/api`)
        yield put(receiveCommentUpdate(result.data, id))
        yield put(updateCommentCounter(
            {
                ...commentState.counterInfo,
                new: (commentState.counterInfo.new - commentState.selectedPageComment.unread_count)
            }
        ))
    } catch (error) {
        console.log('Update comment to read got error: ', error)
        yield put(requestCommentUpdateFail)
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
        yield put(updateCommentCounter(
            {
                ...commentState.counterInfo,
                total: (commentState.counterInfo.total + 1)
            }
        ))
        yield call(updateApi, `/feedback/comment/${id}/mark_read/api`)
    } catch (error) {
        console.log('Post comment create got error: ', error)
        yield put(requestCommentCreateFail)
    }
}

function* postCommentReply(action) {
    const { id, message } = action
    const commentState = yield select(state => state.comment)
    const postData = {
        message: message
    }

    yield put(requestCommentReply)
    try {
        const result = yield call(postApi, `/feedback/comment/${id}/reply`, postData)

        let updateCounterInfo = {}
        if (result.data.is_first_reply) {
            updateCounterInfo['responded'] = (commentState.counterInfo.responded + 1)
        }

        yield put(receiveCommentReply(result.data, id))
        yield put(updateCommentCounter(
            {
                ...commentState.counterInfo,
                ...updateCounterInfo
            }
        ))
    } catch (error) {
        console.log('Post comment create got error: ', error)
        yield put(requestCommentReplyFail)
    }
}

function* deleteComment(action) {
    const { id } = action
    const commentState = yield select(state => state.comment)

    yield put(requestCommentDelete)
    try {
        const result = yield call(deleteApi, `/feedback/comment/${id}`)
        let updateCounterInfo = {
            total: (commentState.counterInfo.total - 1)
        }
        if (result.data.has_reply) {
            updateCounterInfo['responded'] = (commentState.counterInfo.responded - 1)
        }
        yield put(receiveCommentDelete(id, result.data))
        yield put(updateCommentCounter(
            {
                ...commentState.counterInfo,
                ...updateCounterInfo
            }
        ))
    } catch (error) {
        console.log('Delete comment got error: ', error)
        yield put(requestCommentDeleteFail)
    }
}

export default function* watchCOMMENT() {
    yield takeLatest(GET_COMMENT_LIST, fetchCommentList)
    yield takeLatest(GET_COMMENT, fetchPageComments)
    yield takeLatest(SUBMIT_COMMENT_CREATE, postCommentCreate)
    yield takeLatest(UPDATE_COMMENT_STATUS, updateCommentStatus)
    yield takeLatest(SUBMIT_COMMENT_REPLY, postCommentReply)
    yield takeLatest(DELETE_COMMENT, deleteComment)
}
