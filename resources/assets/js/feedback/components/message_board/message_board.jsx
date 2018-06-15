import React from 'react'
import Header from './header/header'
import Message from './message'
import TypingBar from './typing_bar'


class MessageBoard extends React.Component {
    constructor(props) {
        super(props)
    }

    isHost(log) {
        return true
    }

    showTypingBar() {
        const { selectPageID, selectedPageType, messageInputPlaceholder, sendBtnStr, changeCommentCreate, newCommentInputs, submitCommentCreate, messagePostStr } = this.props
        return (<TypingBar
                    id={ selectPageID }
                    type={ selectedPageType }
                    inputPlaceholder={ messageInputPlaceholder }
                    sendBtnStr={ messagePostStr }
                    changeInput={ changeCommentCreate }
                    newCommentInputs={ newCommentInputs }
                    commentSubmit={ submitCommentCreate }
                  ></TypingBar>)
    }

    showMessage(comment, key) {
      const { datetimeFormate, hostName, messageReplyStr, messageDeleteStr, messagePostStr, submitCommentReply, deleteComment, isAdmin } = this.props

      return (
        <div key={ key }>
          <Message
            id={ comment.id }
            dateTime={ comment.created_at }
            name={ comment.payload.name }
            message={ comment.payload.message }
            commentReplySubmit={ submitCommentReply }
            deleteComment={ deleteComment }
            datetimeFormate={ datetimeFormate }
            isHost={ false }
            replyStr={ messageReplyStr }
            deleteStr={ messageDeleteStr }
            postStr={ messagePostStr }
            isAdmin= { isAdmin }
            ></Message>
            { comment.hasOwnProperty('feedback_replies') && (comment.feedback_replies.map((reply, index) => {
                return (
                  <Message key={ index }
                    dateTime={ reply.created_at }
                    message= {reply.payload.message}
                    datetimeFormate={ datetimeFormate }
                    isHost={ true }
                    showReplybtn= { false }
                  ></Message>
                )
              }))
            }
        </div>
      )
    }

    render() {
        const { data, isMobile, headerOrderStr, headerTotalUnitStr, goBackPath } = this.props
        const classMobileClearfix = isMobile ? 'px-0':''

        return (
          <div className="row message-board">
            <div className="col-12">
                <Header isMobile={ isMobile } order={ headerOrderStr } unit={ headerTotalUnitStr } total={ data.length } goBackPath={ goBackPath }/>
            </div>
            <div className="col-12">
            { this.showTypingBar() }
            </div>
            <div className="col-12">
              { data.map((comment, index) => {
                  return this.showMessage(comment, index)
                })
              }
            </div>
          </div>
        )
    }

}

export default MessageBoard
