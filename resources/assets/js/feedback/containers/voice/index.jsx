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
import { getVoiceList, getVoiceMessage } from '../../actions/voice'
import { MESSAGE_DATE_FORMATE, DATA_LIST_DATE_FORMATE } from '../../constants'



class Index extends React.Component {
    constructor(props) {
        super(props)
    }

    componentWillMount(){
        const { getVoiceList } = this.props
        getVoiceList()
    }

    arrangeList() {
        const { list } = this.props
        let dataList = []

        list.forEach((data) =>{
            const { id, is_read, group_name, message, updated_at } = data
            const phone = group_name
            const content = message
            const date = updated_at ? moment(updated_at).format(DATA_LIST_DATE_FORMATE):''
            const dataObj = {
                id: id,
                isRead: is_read? is_read : true ,
                fields: [
                    (<PhoneField data={phone} key={`phone-${id}`}></PhoneField>),
                    (<DateField data={date} key={`date-${id}`}></DateField>),
                    (<ContentField data={content} key={`content-${id}`} isRead={true}></ContentField>)
                ]
            }

            dataList.push(dataObj)
        })
        return dataList
    }

    arrangeSelectedObj() {
        const { selectedVoice } = this.props
        let selectedObj = null

        if(!(_.isEmpty(selectedVoice))) {
            selectedObj = {
                from: selectedVoice.group_name,
                date: selectedVoice.updated_at ? moment(selectedVoice.updated_at).format(MESSAGE_DATE_FORMATE):''
            }
        }
        return selectedObj
    }

    showDetail() {
        const { messages,
            selectedVoice,
            getVoiceList,
            editPermission } = this.props

        let voiceDetail
        if(!_.isEmpty(selectedVoice)) {
            const selectedId = selectedVoice.id
            voiceDetail = (
                <div className="col-12 col-lg-5 pr-lg-0 d-none d-lg-block">
                  <DetailHeader id={ selectedId }
                    fromStr={ lang.feedback.voice.message_log.from }
                    editPermission={ false }
                    { ...this.arrangeSelectedObj() }></DetailHeader>
                  <MessageLog
                    messageId={ selectedId }
                    data={ messages }
                    selected={ this.arrangeSelectedObj() }
                    updateListData={ getVoiceList }
                    enableTypingBar={ false }></MessageLog>
                </div>
            )
        }
        return voiceDetail
    }

    arrangeJumbotronContent() {
        return (<TextContentWithTriggerButton
            title={ lang.feedback.voice.description.sub_title }
            description={ lang.feedback.voice.description.content }
            showDescriptionBtnStr={ lang.feedback.voice.description.show_btn }
            hideDescriptionBtnStr={ lang.feedback.voice.description.hide_btn } />)
    }

    arrangeCounter(isMobile=false) {
        const { counter } = this.props
        const newBarNumeratorObj = {
            numerator: counter.new,
            numeratorTitle: lang.feedback.voice.counter.new,
        }
        const newBarDenominatorObj = {
            denominator: counter.total,
            denominatorTitle: lang.feedback.voice.counter.total,
        }

        return isMobile?
        [ newBarNumeratorObj,
            newBarDenominatorObj ]:
        [ {...newBarNumeratorObj,
            ...newBarDenominatorObj,
            yellowBg: false} ]
    }

    render() {
        const { list,
            getVoiceMessage,
            messages,
            selectedVoice } = this.props
        const selectedId = !(_.isEmpty(selectedVoice))? selectedVoice.id : null
        const rootPath = '/voice'

        return (
          <div>
            <div className="py-4">
              <Jumbotron
                title={ lang.feedback.voice.title }
                apparentTitle={ lang.feedback.voice.offical_phone_code }
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
                    data={ this.arrangeList() }
                    selectedId={ selectedId }
                    orderStr={ lang.feedback.voice.data_list.order }
                    getDetail={ getVoiceMessage }
                    rootPath={ rootPath }
                    isAdmin={ editPermission }/>
                </div>
                { this.showDetail() }
              </div>
            </div>
          </div>

        )
    }
}

const mapStateToProps = state => {
    const { voice, auth } = state
    return {
        list: voice.list,
        messages: voice.messages,
        selectedVoice: voice.selectedVoice,
        counter: voice.counterInfo,
        editPermission: auth.isAdmin
    }
}

const mapDispatchToProps = dispatch => {
    return {
        getVoiceList: bindActionCreators(getVoiceList, dispatch),
        getVoiceMessage: bindActionCreators(getVoiceMessage, dispatch),
        dispatch
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Index))
