import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { withRouter, Redirect } from 'react-router-dom'
import MessageBoard from '../../components/message_board/message_board'
import { getCommentList, getPageComments, changeCommentCreate, submitCommentCreate, updateCommentIsRead, changeCommentReply, submitCommentReply, deleteComment } from '../../actions/comment'
import { MESSAGE_DATETIME_FORMATE, PHONE_NUMBER_SECURE_STR } from '../../constants'


class Show extends React.Component {
    constructor(props) {
        super(props)
    }

    componentWillMount(){
        const { getCommentList, getPageComments, match: { params }, list } = this.props

        if(list.length > 0 ) {
            const { id } = params
            getPageComments(parseInt(id))

        }else {
            getCommentList()
        }
    }

    componentDidUpdate(prevProps) {
        const oldList = prevProps.list
        const oldselectedPageComment = prevProps.selectedPageComment
        const { list, selectedPageComment } = this.props
        const { getPageComments, updateCommentIsRead, match: { params }, fetchError, history} = this.props
        const { id } = params
        if(oldList.length < list.length) {

            getPageComments(parseInt(id))
        }

        if(oldselectedPageComment != selectedPageComment && !_.isEmpty(selectedPageComment) && !selectedPageComment.is_read) {
            updateCommentIsRead(parseInt(id))
        }

        if((!_.isEmpty(oldselectedPageComment) && _.isEmpty(selectedPageComment)) || fetchError || _.isEmpty(list)) {
            history.push('/comment')
        }
    }

    arrangeSelectedObj() {
        const { selectedPageComment } = this.props
        let selectedObj = null

        if(!(_.isEmpty(selectedPageComment))) {
            selectedObj = {
                from: selectedPageComment.phone_number ? (PHONE_NUMBER_SECURE_STR + selectedPageComment.phone_number.slice(-4)) : '',
                date: selectedPageComment.created_at ? moment(selectedPageComment.created_at).format(MESSAGE_DATE_FORMATE):''
            }
        }

        return selectedObj
    }

    showContent() {
        const { comments,
            selectedPageComment,
            pageComments,
            newCommentInputs,
            changeCommentCreate,
            submitCommentCreate,
            changeCommentReply,
            replyComment,
            submitCommentReply,
            deleteComment,
            editPermission } = this.props

        let pageCommentDetail
        if(!_.isEmpty(selectedPageComment)) {
            const selectedPageID = selectedPageComment.feedbackable_id
            const selectedPageType = selectedPageComment.feedbackable_type
            const goBackPath = '/comment'

            pageCommentDetail = (
                <div className="col-12 col-lg-5 p-0 d-block">
                  <MessageBoard
                    isMobile={ true }
                    selectPageID={ selectedPageID }
                    selectedPageType={ selectedPageType }
                    data={ pageComments ? pageComments : [] }
                    changeCommentCreate={ changeCommentCreate }
                    newCommentInputs={ newCommentInputs }
                    submitCommentCreate={ submitCommentCreate }
                    submitCommentReply={ submitCommentReply }
                    deleteComment={ deleteComment }
                    headerOrderStr={ lang.feedback.comment.message_board.order }
                    headerTotalUnitStr={ lang.feedback.comment.message_board.unit }
                    hostName={ lang.feedback.comment.message_board.host_name }
                    datetimeFormate={ MESSAGE_DATETIME_FORMATE }
                    messageReplyStr={ lang.feedback.comment.message_board.reply_btn }
                    messageDeleteStr={ lang.feedback.comment.message_board.delete_btn }
                    messagePostStr={ lang.feedback.comment.message_board.post_btn }
                    isAdmin={ editPermission }
                    goBackPath={ goBackPath }
                  />
                </div>
            )
        }
        return pageCommentDetail
    }

    render() {
        const { changeCommentReply,
            replyMessage,
            submitCommentReply,
            selectedPageComment,
            deleteComment,
            editPermission } = this.props
        const goBack = '/comment'
        const commentID = selectedPageComment && selectedPageComment.id ? selectedPageComment.id:null

        return (
          <div className="row px-0">
            { this.showContent() }
          </div>
        )
    }

}

const mapStateToProps = state => {
    const { comment, auth } = state
    return {
        list: comment.list,
        pageComments: comment.pageComments,
        selectedPageComment: comment.selectedPageComment,
        replyMessage: comment.replyMessage,
        fetchError: comment.fetchError,
        editPermission: auth.isAdmin
    }
}

const mapDispatchToProps = dispatch => {
    return {
        getPageComments: bindActionCreators(getPageComments, dispatch),
        changeCommentCreate: bindActionCreators(changeCommentCreate, dispatch),
        submitCommentCreate: bindActionCreators(submitCommentCreate, dispatch),
        updateCommentIsRead: bindActionCreators(updateCommentIsRead, dispatch),
        changeCommentReply: bindActionCreators(changeCommentReply, dispatch),
        submitCommentReply: bindActionCreators(submitCommentReply, dispatch),
        getCommentList: bindActionCreators(getCommentList, dispatch),
        deleteComment: bindActionCreators(deleteComment, dispatch),
        dispatch
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Show))
