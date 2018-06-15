import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { withRouter } from 'react-router-dom'
import Jumbotron from '../../components/jumbotron/jumbotron'
import TextContentWithTriggerButton from '../../components/jumbotron/content/text_with_triggerbtn'
import CounterMobile from '../../components/jumbotron/counter/counter_mobile'
import DataList from '../../components/data_list/data_list'
import DetailHeader from '../../components/detail_header/header'
import MessageLog from '../../components/message_log/message_log'
import PhoneField from '../../components/data_list/field/phone'
import DateField from '../../components/data_list/field/date'
import ContentField from '../../components/data_list/field/content'
import { getSmsList, getSmsMessage, updateSmsIsRead, changeSmsReply, submitSmsReply, deleteSms } from '../../actions/sms'
import { MESSAGE_DATE_FORMATE, PHONE_NUMBER_SECURE_STR, DATA_LIST_DATE_FORMATE } from '../../constants'



class Index extends React.Component {
    constructor(props) {
        super(props)
    }

    componentWillMount(){
        const { getSmsList, pagination } = this.props
        getSmsList(pagination.current)
    }

    componentWillReceiveProps(nextProp) {
        const { selectedSms, getSmsList, pagination }  = this.props
        const newSelected  = nextProp.selectedSms
        if (!_.isEmpty(selectedSms) && _.isEmpty(newSelected)) {
            getSmsList(pagination.current)
        }
    }

    arrangeSmsList() {
        const { list } = this.props
        let smsList = []

        list.forEach((sms) =>{
            const { id, is_read } = sms
            const phone = sms.mask_phone
            const content = sms.message
            const date = sms.created_at ? moment(sms.created_at).format(DATA_LIST_DATE_FORMATE):''
            const smsObj = {
                id: id,
                isRead: is_read,
                fields: [
                    (<PhoneField data={phone} key={`phone-${id}`}></PhoneField>),
                    (<DateField data={date} key={`date-${id}`}></DateField>),
                    (<ContentField data={content} key={`content-${id}`} isRead={is_read}></ContentField>)
                ]
            }

            smsList.push(smsObj)
        })
        return smsList
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

    showSmsDetail() {
        const { messages,
            selectedSms,
            changeSmsReply,
            replyMessage,
            submitSmsReply,
            deleteSms,
            editPermission } = this.props

        let smsDetail
        if(!_.isEmpty(selectedSms)) {
            const selectedId = selectedSms.id
            smsDetail = (
                <div className="col-12 col-lg-5 pr-lg-0 d-none d-lg-block">
                  <DetailHeader id={selectedId}
                    fromStr={ lang.feedback.sms.message_log.from }
                    deleteBtnStr={ lang.feedback.sms.message_log.delete_btn }
                    deleteAction={ deleteSms }
                    editPermission={ editPermission }
                    { ...this.arrangeSelectedObj() }></DetailHeader>
                  <MessageLog
                    messageId={selectedId}
                    data={ messages }
                    selected={ this.arrangeSelectedObj() }
                    sendBtnStr={ lang.feedback.sms.message_log.send_btn }
                    messageInputPlaceholder={ lang.feedback.sms.message_log.message_input_placeholder }
                    changeTypingInput={ changeSmsReply }
                    typingValue={ replyMessage }
                    submitTyping={ submitSmsReply }
                    enableTypingBar={ editPermission }></MessageLog>
                </div>
            )
        }
        return smsDetail
    }

    arrangeJumbotronContent() {
        return (<TextContentWithTriggerButton
            title={ lang.feedback.sms.description.sub_title }
            description={ lang.feedback.sms.description.content }
            showDescriptionBtnStr={ lang.feedback.sms.description.show_btn }
            hideDescriptionBtnStr={ lang.feedback.sms.description.hide_btn } />)
    }

    arrangeCounter(isMobile=false) {
        const { counter } = this.props
        const newBarNumeratorObj = {
            numerator: counter.new,
            numeratorTitle: lang.feedback.sms.counter.new,
        }
        const newBarDenominatorObj = {
            denominator: counter.total,
            denominatorTitle: lang.feedback.sms.counter.total,
        }
        const respondedBarNumeratorObj = {
            numerator: counter.responded,
            numeratorDisplayNumber: counter.unresponded,
            numeratorTitle: lang.feedback.sms.counter.unresponded,
        }
        const respondedBarDenominatorObj = {
            denominator: counter.total,
            denominatorDisplayNumber: counter.responded,
            denominatorTitle: lang.feedback.sms.counter.responded,
        }
        return isMobile?
        [ newBarNumeratorObj,
            newBarDenominatorObj,
            respondedBarNumeratorObj,
            respondedBarDenominatorObj ]:
        [ {...newBarNumeratorObj,
            ...newBarDenominatorObj,
            yellowBg: false},
        {...respondedBarNumeratorObj,
            ...respondedBarDenominatorObj,
            yellowBg: true} ]
    }

    render() {
        const { list,
            getSmsMessage,
            messages,
            selectedSms,
            updateSmsIsRead,
            pagination,
            getSmsList } = this.props
        const selectedId = !(_.isEmpty(selectedSms))? selectedSms.id : null
        const smsRootPath = '/sms'

        return (
        <div>
            <div className="py-4">
                <Jumbotron
                    title={ lang.feedback.sms.title }
                    apparentTitle={ lang.feedback.sms.offical_phone_code }
                    content={ this.arrangeJumbotronContent() }
                    counterData={ this.arrangeCounter() }
                    />
            </div>
            <div className="d-lg-none container-fluid">
                <CounterMobile
                    data={ this.arrangeCounter(true) }
                />
            </div>
            <div className="container-fluid">
                <div className="row">
                    <div className="col-12 col-lg-7">
                        <DataList
                            data={ this.arrangeSmsList(true) }
                            selectedId={ selectedId }
                            orderStr={ lang.feedback.sms.data_list.order }
                            getDetail={ getSmsMessage }
                            updatedStatus={ updateSmsIsRead }
                            rootPath={ smsRootPath }
                            isAdmin={ editPermission }
                            pagination={ pagination }
                            pageChange={ getSmsList }/>
                    </div>
                    { this.showSmsDetail() }
                </div>
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
        counter: sms.counterInfo,
        replyMessage: sms.replyMessage,
        editPermission: auth.isAdmin,
        pagination: sms.pagination
    }
}

const mapDispatchToProps = dispatch => {
    return {
        getSmsList: bindActionCreators(getSmsList, dispatch),
        getSmsMessage: bindActionCreators(getSmsMessage, dispatch),
        updateSmsIsRead: bindActionCreators(updateSmsIsRead, dispatch),
        changeSmsReply: bindActionCreators(changeSmsReply, dispatch),
        submitSmsReply: bindActionCreators(submitSmsReply, dispatch),
        deleteSms: bindActionCreators(deleteSms, dispatch),
        dispatch
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Index))
