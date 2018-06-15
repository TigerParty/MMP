import { fork, all } from 'redux-saga/effects'
import commentSaga from './comment'


function* sagas() {
    yield all([
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
