import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { withRouter } from 'react-router-dom'
import Jumbotron from '../../components/jumbotron/jumbotron'
import TextContentWithTriggerButton from '../../components/jumbotron/content/text_with_triggerbtn'
import CounterMobile from '../../components/jumbotron/counter/counter_mobile'
import DataList from '../../components/data_list/data_list'
import TitleField from '../../components/data_list/field/title'
import DateField from '../../components/data_list/field/date'
import ContentField from '../../components/data_list/field/content'
import MessageBoard from '../../components/message_board/message_board'
import { getCommentList, getPageComments, updateCommentIsRead, changeCommentCreate, submitCommentCreate, changeCommentReply, submitCommentReply, deleteComment } from '../../actions/comment'
import { DATA_LIST_DATE_FORMATE, MESSAGE_DATETIME_FORMATE } from '../../constants'


class Index extends React.Component {
    constructor(props) {
        super(props)
    }

    componentWillMount(){
        const { getCommentList, pagination } = this.props
        getCommentList(pagination.current)
    }

    componentWillReceiveProps(nextProp) {
        const { selectedPageComment, getCommentList, pagination } = this.props
        const newSelected  = nextProp.selectedPageComment
        if (!_.isEmpty(selectedPageComment) && _.isEmpty(newSelected)) {
            getCommentList(pagination.current)
        }
    }

    arrangeCommentList() {
        const { list } = this.props
        let commentList = []

        list.forEach((page) =>{
            const { id, unread_count } = page
            const date = page.last_created_at ? moment(page.last_created_at.date).format(DATA_LIST_DATE_FORMATE):''
            const pageTitle = page.page_title
            const commentObj = {
                id: id,
                isRead: unread_count == 0,
                fields: [
                    (<TitleField data={pageTitle} key={`name-${id}`} isRead={unread_count == 0}></TitleField>),
                    (<DateField data={date} key={`date-${id}`}></DateField>)
                ]
            }

            commentList.push(commentObj)
        })
        return commentList
    }

    showPageComments() {
        const { comments,
            selectedPageComment,
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
        if(!_.isEmpty(selectedPageComment)) {
            const selectedPageID = selectedPageComment.id
            const selectedPageType = selectedPageComment.entity

            pageCommentDetail = (
                <div className="col-12 col-lg-5 pr-lg-0 d-none d-lg-block">
                    <MessageBoard
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
        }
        return pageCommentDetail
    }

    arrangeJumbotronContent() {
        return (<TextContentWithTriggerButton
            title={ lang.feedback.comment.description.sub_title }
            description={ lang.feedback.comment.description.content }
            showDescriptionBtnStr={ lang.feedback.comment.description.show_btn }
            hideDescriptionBtnStr={ lang.feedback.comment.description.hide_btn } />)
    }

    arrangeCounter(isMobile=false) {
        const { counter } = this.props
        const newBarNumeratorObj = {
            numerator: counter.new,
            numeratorTitle: lang.feedback.comment.counter.new,
        }
        const newBarDenominatorObj = {
            denominator: counter.total,
            denominatorTitle: lang.feedback.comment.counter.total,
        }
        const respondedBarNumeratorObj = {
            numerator: counter.unresponded,
            numeratorTitle: lang.feedback.comment.counter.unresponded,
        }
        const respondedBarDenominatorObj = {
            denominator: counter.total,
            denominatorDisplayNumber: counter.responded,
            denominatorTitle: lang.feedback.comment.counter.responded,
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
        const {
            editPermission,
            list,
            getPageComments,
            selectedPageComment,
            updateCommentIsRead,
            pagination,
            getCommentList } = this.props
        const selectedId = !(_.isEmpty(selectedPageComment))? selectedPageComment.id : null
        const commentRootPath = '/comment'

        return (
        <div>
            <div className="py-4">
                <Jumbotron
                title={ lang.feedback.comment.title }
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
                    data={ this.arrangeCommentList() }
                    selectedId={ selectedId }
                    orderStr={ lang.feedback.comment.data_list.order }
                    getDetail={ getPageComments }
                    updatedStatus={ updateCommentIsRead }
                    rootPath={ commentRootPath }
                    isAdmin={ editPermission }
                    pagination={ pagination }
                    pageChange={ getCommentList }/>
                </div>
                { this.showPageComments() }
                </div>
            </div>
        </div>
        )
    }
}

const mapStateToProps = state => {
    const { comment, auth } = state

    return {
        list: comment.list,
        pageComments: comment.pageComments,
        selectedPageComment: comment.selectedPageComment,
        newCommentInputs: comment.newCommentInputs,
        counter: comment.counterInfo,
        replyCommentMessage: comment.replyCommentMessage,
        editPermission: auth.isAdmin,
        pagination: comment.pagination
    }
}

const mapDispatchToProps = dispatch => {
    return {
        getCommentList: bindActionCreators(getCommentList, dispatch),
        getPageComments: bindActionCreators(getPageComments, dispatch),
        updateCommentIsRead: bindActionCreators(updateCommentIsRead, dispatch),
        changeCommentCreate: bindActionCreators(changeCommentCreate, dispatch),
        submitCommentCreate: bindActionCreators(submitCommentCreate, dispatch),
        changeCommentReply: bindActionCreators(changeCommentReply, dispatch),
        submitCommentReply: bindActionCreators(submitCommentReply, dispatch),
        deleteComment: bindActionCreators(deleteComment, dispatch),
        dispatch
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Index))

