import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { withRouter, Redirect } from 'react-router-dom'
import HeaderMobile from '../../components/detail_header/header_mobile'
import Carousel from '../../components/carousel/carousel'
import EmailField from '../../components/data_list/field/email'
import DateField from '../../components/data_list/field/date'
import ContentField from '../../components/data_list/field/content'
import Map from '../../components/map/map'
import { getReportList, getReportMessage, updateReportIsRead, deleteReport } from '../../actions/report'
import { MESSAGE_DATE_FORMATE } from '../../constants'


class Show extends React.Component {
    constructor(props) {
        super(props)
    }

    componentWillMount(){
        const { getReportList, getReportMessage, match: { params }, list } = this.props
        if(list.length > 0 ) {
            const { id } = params
            getReportMessage(parseInt(id))

        }else {
            getReportList()
        }
    }

    componentDidUpdate(prevProps) {
        const oldList = prevProps.list
        const oldselectedReport = prevProps.selectedReport
        const { list, selectedReport } = this.props
        const { getReportMessage, updateReportIsRead, match: { params }, fetchError, history} = this.props
        const { id } = params
        if(oldList.length < list.length) {

            getReportMessage(parseInt(id))
        }

        if(_.isEmpty(oldselectedReport) && !_.isEmpty(selectedReport) && !selectedReport.is_read) {
            updateReportIsRead(parseInt(id))
        }

        if((!_.isEmpty(oldselectedReport) && _.isEmpty(selectedReport)) || fetchError || _.isEmpty(list)) {
            history.push('/report')
        }
    }

    arrangeSelectedObj() {
        const { selectedReport } = this.props
        let selectedObj = null

        if(!(_.isEmpty(selectedReport))) {
            selectedObj = {
                from: selectedReport.email,
                date: selectedReport.created_at ? moment(selectedReport.created_at).format(MESSAGE_DATE_FORMATE):''
            }
        }
        return selectedObj
    }

    showContent() {
      const { report } = this.props

      let reportDetail

      if(!_.isEmpty(report)) {
          const { lat, lng, attachments } = report
          reportDetail = (
            <div className="pt-3 content">
              <Carousel data={ attachments } needIndicator={ true } needControl={ true } id="reportCarousel" />
              { lat && lng && (
                <div className="row mb-3">
                  <div className="col-12">
                    <Map lat={ lat } lng={ lng } />
                  </div>
                </div>)}
            </div>
          )
      }

      return reportDetail
    }

    render() {
        const { report,
            selectedReport,
            deleteReport,
            editPermission } = this.props
        const goBack = '/report'
        const reportID = selectedReport && selectedReport.id ? selectedReport.id:null

        return (
          <div className="row px-0">
            <div className="col-12">
              <HeaderMobile id={reportID}
                    goBackPath={ goBack }
                    deleteAction={ deleteReport }
                    editPermission={ editPermission }
                    {...this.arrangeSelectedObj()} />
              { this.showContent() }
            </div>
          </div>
        )
    }

}

const mapStateToProps = state => {
    const { report, auth } = state
    return {
        list: report.list,
        report: report.report,
        selectedReport: report.selectedReport,
        fetchError: report.fetchError,
        editPermission: auth.isAdmin
    }
}

const mapDispatchToProps = dispatch => {
    return {
        getReportMessage: bindActionCreators(getReportMessage, dispatch),
        updateReportIsRead: bindActionCreators(updateReportIsRead, dispatch),
        getReportList: bindActionCreators(getReportList, dispatch),
        deleteReport: bindActionCreators(deleteReport, dispatch),
        dispatch
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Show))
