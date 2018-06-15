import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { withRouter, Redirect } from 'react-router-dom'
import HeaderMobile from '../../components/detail_header/header_mobile'
import MessageLog from '../../components/message_log/message_log'
import { getVoiceList, getVoiceMessage, updateVoiceIsRead } from '../../actions/voice'
import { MESSAGE_DATE_FORMATE } from '../../constants'


class Show extends React.Component {
    constructor(props) {
        super(props)
    }

    componentWillMount(){
        const { getVoiceList, getVoiceMessage, match: { params }, list } = this.props

        if(list.length > 0 ) {
            const { id } = params
            getVoiceMessage(parseInt(id))

        }else {
            getVoiceList()
        }
    }

    componentDidUpdate(prevProps) {
        const oldList = prevProps.list
        const oldselectedVoice = prevProps.selectedVoice
        const { list, selectedVoice } = this.props
        const { getVoiceMessage, updateVoiceIsRead, match: { params }, fetchError, history} = this.props
        const { id } = params
        if(oldList.length < list.length) {

            getVoiceMessage(parseInt(id))
        }

        // if(_.isEmpty(oldselectedVoice) && !_.isEmpty(selectedVoice) && !selectedVoice.is_read) {
        //     updateVoiceIsRead(parseInt(id))
        // }

        if((!_.isEmpty(oldselectedVoice) && _.isEmpty(selectedVoice)) || fetchError || _.isEmpty(list)) {
            history.push('/voice')
        }
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

    showContent() {
        const { messages,
            selectedVoice,
            editPermission } = this.props

        let voiceDetail
        if(!_.isEmpty(selectedVoice)) {
          const voiceID = selectedVoice.id
          voiceDetail = (
                <div className="content voice">
                  <MessageLog
                    messageId={ voiceID }
                    isMobile={ true }
                    data={ messages }
                    selected={ this.arrangeSelectedObj() }
                    enableTypingBar={ false }> </MessageLog>
                </div>
            )
        }
        return voiceDetail
    }

    render() {
        const { messages,
            selectedVoice,
            editPermission } = this.props
        const goBack = '/voice'
        const voiceID = selectedVoice && selectedVoice.id ? selectedVoice.id:null

        return (
          <div className="row px-0">
            <div className="col-12">
              <HeaderMobile id={voiceID}
                goBackPath={ goBack }
                editPermission={ false }
                {...this.arrangeSelectedObj()} />
              { this.showContent() }
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
        fetchError: voice.fetchError,
        editPermission: auth.isAdmin
    }
}

const mapDispatchToProps = dispatch => {
    return {
        getVoiceMessage: bindActionCreators(getVoiceMessage, dispatch),
        updateVoiceIsRead: bindActionCreators(updateVoiceIsRead, dispatch),
        getVoiceList: bindActionCreators(getVoiceList, dispatch),
        dispatch
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Show))
