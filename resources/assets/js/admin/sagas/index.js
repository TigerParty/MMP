import { all, fork } from 'redux-saga/effects'
import adminSaga from './admin'
import regionSaga from './region'


function* sagas() {
    yield all([
        fork(adminSaga),
        fork(regionSaga)
    ])
}

export default {
    runSagas(sagaMiddleware) {
        [sagas].forEach((saga) => {
            sagaMiddleware.run(saga)
        })
    },
}
