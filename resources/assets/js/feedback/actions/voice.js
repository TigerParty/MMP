import * as actionType from './type'

/*-------- GetVoiceList --------*/

export const requestVoiceList = ({
    type: actionType.REQUEST_VOICE_LIST
})

export const receiveVoiceList = (data) => ({
    type: actionType.RECEIVE_VOICE_LIST,
    payload: data
})

export const requestVoiceListFail = ({
    type: actionType.REQUEST_VOICE_LIST_FAIL
})

export const getVoiceList = () => ({
    type: actionType.GET_VOICE_LIST
})


/*-------- GetVoiceMessage  --------*/

export const requestVoice = (voice) =>({
     type: actionType.REQUEST_VOICE,
     voice
})

export const receiveVoice = (data) => ({
    type: actionType.RECEIVE_VOICE,
    payload: data
})

export const requestVoiceFail = ({
    type: actionType.REQUEST_VOICE_FAIL
})

export const getVoiceMessage = (id) => ({
    type: actionType.GET_VOICE,
    id
})


/*-------- UpdateVoiceIsRead  --------*/

export const requestVoiceUpdate = ({
    type: actionType.REQUEST_VOICE_UPDATE
})

export const receiveVoiceUpdate = (id) => ({
    type: actionType.RECEIVE_VOICE_UPDATE,
    id
})

export const requestVoiceUpdateFail = ({
    type: actionType.REQUEST_VOICE_UPDATE_FAIL
})

export const updateVoiceIsRead = (id) => ({
    type: actionType.UPDATE_VOICE_STATUS,
    id
})


/*-------- UpdateVoiceCounter --------*/

export const updateVoiceCounter =  ({
    type: actionType.UPDATE_VOICE_COUNTER,
})

