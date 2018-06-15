import React from 'react'
import Log from './log'
import TypingBar from './typing_bar'
import { MESSAGE_HOST_TYPE } from '../../constants'


class MessageLog extends React.Component {
    constructor(props) {
        super(props)
        this.scrollToLogViewBottom = this.scrollToLogViewBottom.bind(this)
    }

    isHost(log) {
        return log.type == MESSAGE_HOST_TYPE ? true : false
    }

    showTypingBar() {
        const { enableTypingBar } = this.props
        if(enableTypingBar) {
            const { messageId, messageInputPlaceholder, sendBtnStr, changeTypingInput, typingValue, submitTyping } = this.props
            return (<TypingBar
                        id={messageId}
                        inputPlaceholder={ messageInputPlaceholder }
                        sendBtnStr={ sendBtnStr }
                        changeInput={ changeTypingInput }
                        inputValue={ typingValue }
                        inputSubmit={ submitTyping }></TypingBar>)
        }
    }

    scrollToLogViewBottom(options=true) {
        const { isMobile } = this.props
        const logListBottomEle = this.refs.endLogList
        if(isMobile) {
            return logListBottomEle.scrollIntoView(options)
        }
        logListBottomEle.parentNode.scrollTop = logListBottomEle.offsetTop
    }

    componentDidUpdate(prevProps) {
        const oldData = prevProps.data
        const { data } = this.props
        if(oldData !== data) {
            if(oldData.length == 0 && data.length >0) {
                this.scrollToLogViewBottom()
                return
            }
            this.scrollToLogViewBottom({ behavior: "smooth" })
        }

    }

    render() {
        const { data, isMobile } = this.props
        const classMobileClearfix = isMobile ? 'px-0':''

        return (
          <div className="row message-log">
            <div className={`col-12 ${classMobileClearfix}`}>
              <div className="bg-white
                text-white
                py-3
                log-list"
                ref="scrollLogListView">
                { data.map((log, index) => {
                      return (
                          <Log key={ index }
                              message={ log.message }
                              audio={ log.audio }
                              isHost={ this.isHost(log) }
                              time={ log.updated_at || log.created_at  }></Log>
                      )
                  })
                }
               <div ref="endLogList"></div>
              </div>
            </div>
            <div className={`col-12 mt-1 ${classMobileClearfix}`}>
                { this.showTypingBar() }
            </div>
          </div>
        )
    }

}

export default MessageLog
