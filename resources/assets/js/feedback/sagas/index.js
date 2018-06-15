import { all, fork } from 'redux-saga/effects'
import smsSaga from './sms'
import reportSaga from './report'
import voiceSaga from './voice'
import commentSaga from './comment'


function* sagas() {
    yield all([
        fork(smsSaga),
        fork(reportSaga),
        fork(voiceSaga),
        fork(commentSaga)
    ])
}

export default {
    runSagas(sagaMiddleware) {
        [sagas].forEach((saga) => {
            sagaMiddleware.run(saga)
        })
    },
}
