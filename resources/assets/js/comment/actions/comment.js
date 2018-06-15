import * as actionType from './type'

/*-------- GetPageCommentMessages  --------*/

export const requestPageComments = () =>({
    type: actionType.REQUEST_COMMENT
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


/*-------- PostCommentCreate  --------*/

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

export const receiveCommentDelete = (id, payload) =>({
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
