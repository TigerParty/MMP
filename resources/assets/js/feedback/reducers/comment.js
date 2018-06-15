import * as actionType  from '../actions/type'
import { counterCalculate } from './index'

const initState = {
    list: [],
    pageComments: [],
    isFetching: false,
    fetchError: false,
    isReadUpdating: false,
    selectedPageComment: null,
    counterInfo: {
        new: 0,
        total: 0,
        unresponded: 0,
        responded: 0
    },
    pagination: {
        current: 1,
        from: 0,
        to: 0,
        total: 0
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
        case actionType.REQUEST_COMMENT_LIST:
            return {
                ...state,
                isFetching: true
            }
        case actionType.RECEIVE_COMMENT_LIST:
            const commentList = action.payload
            const { itemsPerPage, current, total } = action.pagination
            const from = total > 0 ? ((current - 1) * itemsPerPage + 1) : 0
            const to = current * itemsPerPage < total && total > 0 ? (current * itemsPerPage) : total

            return {
                ...state,
                isFetching: false,
                list: commentList,
                pagination: {
                    current,
                    from,
                    to,
                    total
                }
            }
        case actionType.REQUEST_COMMENT_LIST_FAIL:
            return {
                ...state,
                isFetching: false,
                list: []
            }
        case actionType.REQUEST_COMMENT:
            return {
                ...state,
                isFetching: true,
                selectedPageComment: action.comment,
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
        case actionType.REQUEST_COMMENT_UPDATE:
            return {
                ...state,
                isReadUpdating: true
            }
        case actionType.RECEIVE_COMMENT_UPDATE:
            return {
                ...state,
                isReadUpdating: false,
                list: state.list.map(data => data.id === action.id ?
                    { ...data, unread_count: 0 } : data
                )
            }
        case actionType.REQUEST_COMMENT_UPDATE_FAIL:
            return {
                ...state,
                isReadUpdating: false
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
                            comment_count: page.comment_count-1,
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
                pageComments: newPageComments,
                selectedPageComment: newPageComments.length == 0 ? null : state.selectedPageComment
            }
        case actionType.REQUEST_COMMENT_DELETE_FAIL:
            return {
                ...state,
                isDeleting: false
            }
        case actionType.UPDATE_COMMENT_COUNTER:
            return {
                ...state,
                counterInfo: {
                    new: action.payload.new,
                    total: action.payload.total,
                    unresponded: (action.payload.total - action.payload.responded),
                    responded: action.payload.responded,
                }
            }
        default:
            return state
    }
}

export default comment
