import { all, fork } from 'redux-saga/effects'
import userSaga from './user'


function* sagas() {
    yield all([
        fork(userSaga)
    ])
}

export default {
    runSagas(sagaMiddleware) {
        [sagas].forEach((saga) => {
            sagaMiddleware.run(saga)
        })
    },
}
