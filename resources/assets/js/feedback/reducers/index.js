import { combineReducers } from 'redux'
import auth from './auth'
import sms from './sms'
import report from './report'
import voice from './voice'
import comment from './comment'


export const counterCalculate = (list=[], needRespond=false, isComment=false) =>{
    let counterInfoObj = {}
    if(isComment) {
            let unread = 0
            let total = 0
            let unresponded = 0
            let responed = 0
        for (var pageTitle in list ) {
            unread += list[pageTitle].unread_count
            total += list[pageTitle].comment_count
            unresponded += list[pageTitle].comment_count - list[pageTitle].replied_count
            responed += list[pageTitle].replied_count
        }
        counterInfoObj = Object.assign({
            new: unread,
            total: total,
            unresponded: unresponded,
            responed: responed,
        }, counterInfoObj)
    } else {
        const isReadList = _.filter(list, { is_read:0 })

        counterInfoObj = Object.assign({
            new: isReadList.length,
            total: list.length
        }, counterInfoObj)
    }

    if(needRespond && !isComment) {
        const countRespondUnresponded = _.countBy(list, (data) =>{ return data.has_reply})
        counterInfoObj = Object.assign({
            unresponded: countRespondUnresponded[0] ? countRespondUnresponded[0]:0,
            responed: countRespondUnresponded[1] ? countRespondUnresponded[1]:0
        }, counterInfoObj)
    }

    return counterInfoObj
}

const feedbackApp = combineReducers ({
    auth,
    sms,
    report,
    voice,
    comment,
})

export default feedbackApp
