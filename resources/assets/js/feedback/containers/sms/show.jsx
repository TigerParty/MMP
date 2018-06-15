import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { withRouter, Redirect } from 'react-router-dom'
import HeaderMobile from '../../components/detail_header/header_mobile'
import MessageLog from '../../components/message_log/message_log'
import { getSmsList, getSmsMessage, updateSmsIsRead, changeSmsReply, submitSmsReply, deleteSms } from '../../actions/sms'
import { MESSAGE_DATE_FORMATE, PHONE_NUMBER_SECURE_STR } from '../../constants'


class Show extends React.Component {
    constructor(props) {
        super(props)
    }

    componentWillMount(){
        const { getSmsList, getSmsMessage, match: { params }, list } = this.props

        if(list.length > 0 ) {
            const { id } = params
            getSmsMessage(parseInt(id))

        }else {
            getSmsList()
        }
    }

    componentDidUpdate(prevProps) {
        const oldList = prevProps.list
        const oldselectedSms = prevProps.selectedSms
        const { list, selectedSms } = this.props
        const { getSmsMessage, updateSmsIsRead, match: { params }, fetchError, history} = this.props
        const { id } = params
        if(oldList.length < list.length) {

            getSmsMessage(parseInt(id))
        }

        if(_.isEmpty(oldselectedSms) && !_.isEmpty(selectedSms) && !selectedSms.is_read) {
            updateSmsIsRead(parseInt(id))
        }

        if((!_.isEmpty(oldselectedSms) && _.isEmpty(selectedSms)) || fetchError || _.isEmpty(list)) {
            history.push('/sms')
        }
    }

    arrangeSelectedObj() {
        const { selectedSms } = this.props
        let selectedObj = null

        if(!(_.isEmpty(selectedSms))) {
            selectedObj = {
                from: selectedSms.mask_phone,
                date: selectedSms.created_at ? moment(selectedSms.created_at).format(MESSAGE_DATE_FORMATE):''
            }
        }

        return selectedObj
    }

    showContent() {
        const { messages,
            selectedSms,
            changeSmsReply,
            replyMessage,
            submitSmsReply,
            editPermission } = this.props

        let smsDetail
        if(!_.isEmpty(selectedSms)) {
          const smsID = selectedSms.id
          smsDetail = (
                <div className="content sms">
                  <MessageLog
                    messageId={ smsID }
                    isMobile={ true }
                    data={ messages }
                    selected={ this.arrangeSelectedObj() }
                    sendBtnStr={ lang.feedback.sms.message_log.send_btn }
                    messageInputPlaceholder={ lang.feedback.sms.message_log.message_input_placeholder }
                    changeTypingInput={ changeSmsReply }
                    typingValue={ replyMessage }
                    submitTyping={ submitSmsReply }
                    enableTypingBar={ editPermission }> </MessageLog>
                </div>
            )
        }
        return smsDetail
    }

    render() {
        const { messages,
            changeSmsReply,
            replyMessage,
            submitSmsReply,
            selectedSms,
            deleteSms,
            editPermission } = this.props
        const goBack = '/sms'
        const smsID = selectedSms && selectedSms.id ? selectedSms.id:null

        return (
          <div className="row px-0">
            <div className="col-12">
              <HeaderMobile id={smsID}
                goBackPath={ goBack }
                deleteAction={ deleteSms }
                editPermission={ editPermission }
                {...this.arrangeSelectedObj()} />
              { this.showContent() }
            </div>
          </div>
        )
    }

}

const mapStateToProps = state => {
    const { sms, auth } = state
    return {
        list: sms.list,
        messages: sms.messages,
        selectedSms: sms.selectedSms,
        replyMessage: sms.replyMessage,
        fetchError: sms.fetchError,
        editPermission: auth.isAdmin
    }
}

const mapDispatchToProps = dispatch => {
    return {
        getSmsMessage: bindActionCreators(getSmsMessage, dispatch),
        updateSmsIsRead: bindActionCreators(updateSmsIsRead, dispatch),
        changeSmsReply: bindActionCreators(changeSmsReply, dispatch),
        submitSmsReply: bindActionCreators(submitSmsReply, dispatch),
        getSmsList: bindActionCreators(getSmsList, dispatch),
        deleteSms: bindActionCreators(deleteSms, dispatch),
        dispatch
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Show))
