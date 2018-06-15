import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import MessageBoard from './components/message_board/message_board'
import { getPageComments, changeCommentCreate, submitCommentCreate, changeCommentReply, submitCommentReply, deleteComment } from './actions/comment'
import { MESSAGE_DATETIME_FORMATE, PHONE_NUMBER_SECURE_STR } from './constants'


class App extends React.Component {
    constructor(props) {
        super(props)
    }

    componentWillMount(){
        const { getPageComments } = this.props
        const id = window.commentId
        getPageComments(parseInt(id))
    }

    showContent() {
        const { comments,
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
        const selectedPageID = window.commentId
        const selectedPageType = window.commentType

        pageCommentDetail = (
            <div className="col-12 d-block">
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
              />
            </div>
        )
        return pageCommentDetail
    }

    render() {
        return (
          <div className="container-fluid">
            <div className="row">
              { this.showContent() }
            </div>
          </div>
        )
    }

}

const mapStateToProps = state => {
    const { comment, auth } = state
    return {
        pageComments: comment.pageComments,
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
        changeCommentReply: bindActionCreators(changeCommentReply, dispatch),
        submitCommentReply: bindActionCreators(submitCommentReply, dispatch),
        deleteComment: bindActionCreators(deleteComment, dispatch),
        dispatch
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(App)
