import * as actionType from './type'

/*-------- GetSmsList --------*/

export const requestSmsList = ({
    type: actionType.REQUEST_SMS_LIST
})

export const receiveSmsList = (data, pagination) => ({
    type: actionType.RECEIVE_SMS_LIST,
    payload: data,
    pagination
})

export const requestSmsListFail = ({
    type: actionType.REQUEST_SMS_LIST_FAIL
})

export const getSmsList = (page=1) => ({
    type: actionType.GET_SMS_LIST,
    page
})


/*-------- GetSmsMessage  --------*/

export const requestSms = (sms) =>({
    type: actionType.REQUEST_SMS,
    sms
})

export const receiveSms = (data) => ({
    type: actionType.RECEIVE_SMS,
    payload: data
})

export const requestSmsFail = ({
    type: actionType.REQUEST_SMS_FAIL
})

export const getSmsMessage = (id) => ({
    type: actionType.GET_SMS,
    id
})



/*-------- UpdateSmsIsRead  --------*/

export const requestSmsUpdate = ({
    type: actionType.REQUEST_SMS_UPDATE
})

export const receiveSmsUpdate = (id) => ({
    type: actionType.RECEIVE_SMS_UPDATE,
    id
})

export const requestSmsUpdateFail = ({
    type: actionType.REQUEST_SMS_UPDATE_FAIL
})

export const updateSmsIsRead = (id) => ({
    type: actionType.UPDATE_SMS_STATUS,
    id
})


/*-------- PostSmsReply  --------*/

export const requestSmsReply = ({
    type: actionType.REQUEST_SMS_REPLY
})

export const receiveSmsReply = (payload, id) => ({
    type: actionType.RECEIVE_SMS_REPLY,
    payload,
    id
})

export const requestSmsReplyFail = ({
     type: actionType.REQUEST_SMS_REPLY_FAIL
})

export const submitSmsReply = (id) => ({
    type: actionType.SUBMIT_SMS_REPLY,
    id
})

export const changeSmsReply = (value) => ({
    type: actionType.INPUT_SMS_REPLY,
    value
})


/*-------- DeleteSms --------*/

export const requestSmsDelete = ({
    type: actionType.REQUEST_SMS_DELETE
})

export const receiveSmsDelete = (payload) =>({
    type: actionType.RECEIVE_SMS_DELETE,
    payload
})

export const requestSmsDeleteFail = ({
    type: actionType.REQUEST_SMS_DELETE_FAIL
})

export const deleteSms = (id) => ({
    type: actionType.DELETE_SMS,
    id
})


/*-------- UpdateSmsCounter --------*/

export const updateSmsCounter = (payload)=> ({
    type: actionType.UPDATE_SMS_COUNTER,
    payload
})
