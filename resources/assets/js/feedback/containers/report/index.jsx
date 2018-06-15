import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { withRouter } from 'react-router-dom'
import Jumbotron from '../../components/jumbotron/jumbotron'
import ImgContent from '../../components/jumbotron/content/image'
import CounterMobile from '../../components/jumbotron/counter/counter_mobile'
import DataList from '../../components/data_list/data_list'
import DetailHeader from '../../components/detail_header/header'
import Carousel from '../../components/carousel/carousel'
import TitleField from '../../components/data_list/field/title'
import DateField from '../../components/data_list/field/date'
import Map from '../../components/map/map'
import { getReportList, getReportMessage, updateReportIsRead, deleteReport } from '../../actions/report'
import { MESSAGE_DATE_FORMATE, DATA_LIST_DATE_FORMATE } from '../../constants'



class Index extends React.Component {
    constructor(props) {
        super(props)
    }

    componentWillMount(){
        const { getReportList, pagination } = this.props
        getReportList(pagination.current)
    }

    componentWillReceiveProps(nextProp) {
        const { selectedReport, getReportList, pagination }  = this.props
        const newSelected  = nextProp.selectedReport
        if (!_.isEmpty(selectedReport) && _.isEmpty(newSelected)) {
            getReportList(pagination.current)
        }

    }

    arrangeReportList(isMobile = false) {
        const { list } = this.props
        let reportList = []

        list.forEach((report) =>{
            const { id, is_read, comment, email } = report
            const content = comment
            const date = report.created_at ? moment(report.created_at).format(DATA_LIST_DATE_FORMATE):''
            const reportObj = {
                id: id,
                isRead: is_read,
                fields: [
                    (<TitleField data={content} key={`name-${id}`} isRead={is_read}></TitleField>),
                    (<DateField data={date} key={`date-${id}`} ></DateField>)
                ]
            }

            reportList.push(reportObj)
        })
        return reportList
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

    showReportDetail() {
        const { report,
            selectedReport,
            deleteReport,
            editPermission } = this.props

        let reportDetail
        if(!_.isEmpty(report)) {
            const selectedId = selectedReport.id
            const { lat, lng, attachments } = report
            reportDetail = (
                <div className="col-12 col-lg-5 pr-lg-0 d-none d-lg-block">
                  <DetailHeader id={selectedId}
                    deleteBtnStr={ lang.feedback.report.message_log.delete_btn }
                    deleteAction={ deleteReport }
                    editPermission={ editPermission }
                    { ...this.arrangeSelectedObj() }></DetailHeader>
                  <div className="col-12 bg-white py-3">
                    <Carousel data={ attachments } needIndicator={ true } needControl={ true } id="reportCarousel" />
                    { report.lat && report.lng &&
                      <div className="row mt-5">
                        <div className="col-12">
                          <Map lat={ lat } lng={ lng } />
                        </div>
                      </div>
                    }
                  </div>
                </div>
            )
        }

        return reportDetail
    }

    arrangeJumbotronContent() {
        return (<ImgContent title={ lang.feedback.report.description.sub_title }  imgPath='/images/icon/google-play.svg'/>)
    }

    arrangeCounter(isMobile=false) {
        const { counter } = this.props

        const newBarNumeratorObj = {
            numerator: counter.new,
            numeratorTitle: lang.feedback.report.counter.new,
        }
        const newBarDenominatorObj = {
            denominator: counter.total,
            denominatorTitle: lang.feedback.report.counter.total,
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
            getReportMessage,
            messages,
            selectedReport,
            updateReportIsRead,
            counter,
            pagination,
            getReportList } = this.props
        const selectedId = !(_.isEmpty(selectedReport))? selectedReport.id : null
        const reportRootPath = '/report'

        return (
        <div>
            <div className="py-4">
                <Jumbotron
                    title={ lang.feedback.report.title }
                    apparentTitle={ lang.feedback.report.offical_phone_code }
                    content={ this.arrangeJumbotronContent() }
                    counterData={ this.arrangeCounter() }/>
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
                        data={ this.arrangeReportList() }
                        selectedId={ selectedId }
                        orderStr={ lang.feedback.report.data_list.order }
                        getDetail={ getReportMessage }
                        updatedStatus={ updateReportIsRead }
                        rootPath={ reportRootPath}
                        isAdmin={ editPermission }
                        pagination={pagination}
                                    pageChange={getReportList}/>
                    </div>
                    { this.showReportDetail() }
                </div>
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
        counter: report.counterInfo,
        editPermission: auth.isAdmin,
        pagination: report.pagination
    }
}

const mapDispatchToProps = dispatch => {
    return {
        getReportList: bindActionCreators(getReportList, dispatch),
        getReportMessage: bindActionCreators(getReportMessage, dispatch),
        updateReportIsRead: bindActionCreators(updateReportIsRead, dispatch),
        deleteReport: bindActionCreators(deleteReport, dispatch),
        dispatch
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Index))
