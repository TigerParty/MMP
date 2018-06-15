import React from 'react'
import Avatar from '../../../components/avatar/avatar'
import swal from 'sweetalert'


class Message extends React.Component {
    constructor(props) {
        super(props)
        this.showContent = this.showContent.bind(this)
        this.replyBoxSwitch = this.replyBoxSwitch.bind(this)
        this.handleInputChange = this.handleInputChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.handleDelete = this.handleDelete.bind(this)
        this.state = {
            replyBoxOpened: false,
            replyMessage: null,
        }
    }

    replyBoxSwitch() {
        this.setState({replyBoxOpened: !this.state.replyBoxOpened})
    }

    handleInputChange() {
        this.setState({replyMessage: this.refs.replyMessage.value})
    }

    handleSubmit(e) {
        e.preventDefault()
        const { id, commentReplySubmit } = this.props
        commentReplySubmit(id, this.state.replyMessage)
        this.setState({replyMessage: null, replyBoxOpened: false})
    }

    handleDelete() {
        const { id, deleteComment } = this.props
        swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover it!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            deleteComment(id)
          }
        })
    }

    showContent() {
        const { message, audio } = this.props
        if(message) {
            return message
        }
        if(audio) {
            return (
              <audio controls>
                <source src={audio} type="audio/mpeg"/>
                Your browser does not support the audio element.
              </audio>
            )
        }
    }

    render() {
        const { dateTime, isHost, name, message, datetimeFormate, isAdmin, replyStr, deleteStr, postStr } = this.props
        const dateTimeStr = dateTime? moment(dateTime).format(datetimeFormate):''
        const bgClass = isHost? 'reply':''
        const replyName = lang.site.shorthead_title

        return (
          <div className="row mx-0 message">
            <div className={`col-12
              align-self-center
              p-3
              font-size-15
              content
              ${bgClass}`}>
              <div className="row">
                <div className="col-auto align-self-center">
                  { isHost? (<Avatar/>):(<img src="./images/icon/face.svg"/>) }
                </div>
                <div className="col-auto pl-0 font-weight-bold font-size-18 align-self-center">
                  { isHost ? replyName : name }
                </div>
                <div className="col-12 clearfix mt-2">
                  { message }
                </div>
              </div>
              <div className="row mt-3 align-items-center">
                {
                  !isHost && isAdmin && (
                    <div className="col-auto function-btn">
                      <span className="text-primary font-weight-light-bold font-size-18" onClick={ this.replyBoxSwitch }>{ replyStr }</span>
                    </div>
                  )
                }
                {
                  !isHost && isAdmin && (
                    <div className="col-auto function-btn">
                      <span className="text-primary font-weight-light-bold font-size-18" onClick={ this.handleDelete }>{ deleteStr }</span>
                    </div>
                  )
                }
                <div className="col-auto text-gray opacity-0-5 ml-auto">
                  { dateTimeStr }
                </div>
              </div>
            </div>
            {
              this.state.replyBoxOpened && !isHost && isAdmin && (
                <form className="col-12 p-2 reply-box" onSubmit={ this.handleSubmit }>
                  <textarea className="p-2"
                    placeholder={ lang.feedback.comment.message_board.placeholders.reply_box }
                    name="replyMessage"
                    ref="replyMessage"
                    onChange={ this.handleInputChange }
                  ></textarea>
                  <button type="submit">{ postStr }</button>
                </form>
              )
            }
          </div>
        )
    }

}

export default Message
