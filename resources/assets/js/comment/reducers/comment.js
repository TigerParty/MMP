import * as actionType  from '../actions/type'

const initState = {
    list: [],
    pageComments: [],
    isFetching: false,
    fetchError: false,
    isReadUpdating: false,
    counterInfo: {
        new: 0,
        total: 0,
        unresponded: 0,
        responed: 0
    },
    newCommentInputs: {
        name: null,
        email: null,
        message: null,
    },
    replyComment: null,
    isReplying: false,
    isDeleting: false
}

function comment(state = initState, action) {
    switch (action.type) {
        case actionType.REQUEST_COMMENT:
            return {
                ...state,
                isFetching: true,
                pageComments: [],
                fetchError: false
            }
        case actionType.RECEIVE_COMMENT:

            return {
                ...state,
                isFetching: false,
                pageComments: action.payload.page_comments,
                newCommentInputs: {
                    name: null,
                    email: null,
                    message: null,
                }
            }
        case actionType.REQUEST_COMMENT_FAIL:
            return {
                ...state,
                isFetching: false,
                pageComments: [],
                fetchError: true
            }
        case actionType.INPUT_COMMENT_CREATE:
            return {
                ...state,
                newCommentInputs: action.values
            }
        case actionType.REQUEST_COMMENT_CREATE:
            return {
                ...state,
            }
        case actionType.REQUEST_COMMENT_CREATE_FAIL:
            return {
                ...state,
            }
        case actionType.RECEIVE_COMMENT_CREATE:
            return {
                ...state,
                list: state.list.map(data => data.id === action.id ?
                    { ...data, comment_count: data.comment_count+1 } : data
                ),
                pageComments: [action.payload, ...state.pageComments],
                newCommentInputs: {
                    name: null,
                    email: null,
                    message: null,
                }
            }
        case actionType.INPUT_COMMENT_REPLY:
            return {
                ...state,
                replyMessage: action.value
            }
        case actionType.REQUEST_COMMENT_REPLY:
            return {
                ...state,
                isReplying: true,
            }
        case actionType.RECEIVE_COMMENT_REPLY:
            return {
                ...state,
                pageComments: state.pageComments.map(data => data.id === action.id ?
                    {...data, feedback_replies:[action.payload.comment_reply, ...data.feedback_replies]} : data),
            }
        case actionType.REQUEST_COMMENT_REPLY_FAIL:
            return {
                ...state,
                isReplying: false
            }
        case actionType.REQUEST_COMMENT_DELETE:
            return {
                ...state,
                isDeleting: true,
            }
        case actionType.RECEIVE_COMMENT_DELETE:
            let newPageComments = []
            let newList = []
            state.pageComments.forEach(function(comment, index) {
                if(comment.id !== action.id) {
                    newPageComments.push(comment)
                }
            })
            state.list.forEach(function(page) {
                if(page.id === action.payload.page_id_of_deleted_comment) {
                    if(page.comment_count > 1) {
                        newList.push({
                            ...page,
                            comment_count: page.comment_count-1
                        })
                    }
                } else {
                    newList.push(page)
                }
            })
            return {
                ...state,
                isDeleting: false,
                list: newList,
                pageComments: newPageComments
            }
        case actionType.REQUEST_COMMENT_DELETE_FAIL:
            return {
                ...state,
                isDeleting: false
            }
        default:
            return state
    }
}

export default comment
