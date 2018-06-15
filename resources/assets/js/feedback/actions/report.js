import * as actionType from './type'

/*-------- GetReportList --------*/

export const requestReportList = ({
    type: actionType.REQUEST_REPORT_LIST
})

export const receiveReportList = (data, pagination) => ({
    type: actionType.RECEIVE_REPORT_LIST,
    payload: data,
    pagination
})

export const requestReportListFail = ({
    type: actionType.REQUEST_REPORT_LIST_FAIL
})

export const getReportList = (page) => ({
    type: actionType.GET_REPORT_LIST,
    page
})


/*-------- GetReportMessage  --------*/

export const requestReport = (report) =>({
    type: actionType.REQUEST_REPORT,
    report
})

export const receiveReport = (data) => ({
    type: actionType.RECEIVE_REPORT,
    payload: data
})

export const requestReportFail = ({
    type: actionType.REQUEST_REPORT_FAIL
})

export const getReportMessage = (id) => ({
    type: actionType.GET_REPORT,
    id
})


/*-------- UpdateReportIsRead  --------*/

export const requestReportUpdate = ({
    type: actionType.REQUEST_REPORT_UPDATE
})

export const receiveReportUpdate = (id) => ({
    type: actionType.RECEIVE_REPORT_UPDATE,
    id
})

export const requestReportUpdateFail = ({
    type: actionType.REQUEST_REPORT_UPDATE_FAIL
})

export const updateReportIsRead = (id) => ({
    type: actionType.UPDATE_REPORT_STATUS,
    id
})


/*-------- DeleteReport --------*/

export const requestReportDelete = ({
    type: actionType.REQUEST_REPORT_DELETE
})

export const receiveReportDelete = (payload) =>({
    type: actionType.RECEIVE_REPORT_DELETE,
    payload
})

export const requestReportDeleteFail = ({
    type: actionType.REQUEST_REPORT_DELETE_FAIL
})

export const deleteReport = (id) => ({
    type: actionType.DELETE_REPORT,
    id
})


/*-------- UpdateReportCounter --------*/

export const updateReportCounter = (payload) => ({
    type: actionType.UPDATE_REPORT_COUNTER,
    payload
})



