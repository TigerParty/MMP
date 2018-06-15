import * as actionType from './type'

/*-------- GetCommentList --------*/

export const requestCommentList = ({
    type: actionType.REQUEST_COMMENT_LIST
})

export const receiveCommentList = (data, pagination) => ({
    type: actionType.RECEIVE_COMMENT_LIST,
    payload: data,
    pagination
})

export const requestCommentListFail = ({
    type: actionType.REQUEST_COMMENT_LIST_FAIL
})

export const getCommentList = (page = 1) => ({
    type: actionType.GET_COMMENT_LIST,
    page
})


/*-------- GetPageCommentMessages  --------*/

export const requestPageComments = (comment) =>({
    type: actionType.REQUEST_COMMENT,
    comment
})

export const receivePageComments = (data) => ({
    type: actionType.RECEIVE_COMMENT,
    payload: data
})

export const requestPageCommentsFail = ({
    type: actionType.REQUEST_COMMENT_FAIL
})

export const getPageComments = (id) => ({
    type: actionType.GET_COMMENT,
    id
})



/*-------- UpdateCommentIsRead  --------*/

export const requestCommentUpdate = ({
    type: actionType.REQUEST_COMMENT_UPDATE
})

export const receiveCommentUpdate = (payload, id) => ({
    type: actionType.RECEIVE_COMMENT_UPDATE,
    id: id,
    payload
})

export const requestCommentUpdateFail = ({
    type: actionType.REQUEST_COMMENT_UPDATE_FAIL
})

export const updateCommentIsRead = (id) => ({
    type: actionType.UPDATE_COMMENT_STATUS,
    id
})


/*-------- PostCommentReply  --------*/

export const requestCommentCreate = ({
    type: actionType.REQUEST_COMMENT_CREATE
})

export const receiveCommentCreate = (payload, id) => ({
    type: actionType.RECEIVE_COMMENT_CREATE,
    payload,
    id
})

export const requestCommentCreateFail = ({
    type: actionType.REQUEST_COMMENT_REPLY_FAIL
})

export const submitCommentCreate = (id, entityType) => ({
    type: actionType.SUBMIT_COMMENT_CREATE,
    id,
    entityType: entityType
})

export const changeCommentCreate = (values) => ({
    type: actionType.INPUT_COMMENT_CREATE,
    values
})


/*-------- PostCommentReply  --------*/

export const requestCommentReply = ({
    type: actionType.REQUEST_COMMENT_REPLY
})

export const receiveCommentReply = (payload, id) => ({
    type: actionType.RECEIVE_COMMENT_REPLY,
    payload,
    id
})

export const requestCommentReplyFail = ({
    type: actionType.REQUEST_COMMENT_REPLY_FAIL
})

export const submitCommentReply = (id, message) => ({
    type: actionType.SUBMIT_COMMENT_REPLY,
    id,
    message
})

export const changeCommentReply = (value) => ({
    type: actionType.INPUT_COMMENT_REPLY,
    value
})


/*-------- DeleteComment --------*/

export const requestCommentDelete = ({
    type: actionType.REQUEST_COMMENT_DELETE
})

export const receiveCommentDelete = (id, payload) => ({
    type: actionType.RECEIVE_COMMENT_DELETE,
    id,
    payload
})

export const requestCommentDeleteFail = ({
    type: actionType.REQUEST_COMMENT_DELETE_FAIL
})

export const deleteComment = (id) => ({
    type: actionType.DELETE_COMMENT,
    id
})


/*-------- UpdateCommentCounter --------*/

export const updateCommentCounter = (payload) => ({
    type: actionType.UPDATE_COMMENT_COUNTER,
    payload
})
