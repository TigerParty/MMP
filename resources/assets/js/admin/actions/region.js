import * as actionType from './type'


export const receiveRegionLabels = (payload) => ({
    type: actionType.RECEIVE_REGION_LABELS,
    payload
})

/*-------- GetRegionList --------*/

export const requestRegionList = ({
    type: actionType.REQUEST_REGION_LIST
})

export const receiveRegionList = (payload, regionId) => ({
    type: actionType.RECEIVE_REGION_LIST,
    payload,
    id: regionId
})

export const requestRegionListFail = ({
    type: actionType.REQUEST_REGION_LIST_FAIL
})

export const getRegionList = (regionId) => ({
    type: actionType.GET_REGION_LIST,
    id: regionId
})