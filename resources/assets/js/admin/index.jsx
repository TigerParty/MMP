require('../bootstrap')
require('../../scss/app')
require('../../scss/admin/admin')
import 'babel-polyfill'
import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import { createStore, applyMiddleware } from 'redux'
import createSagaMiddleware from 'redux-saga'
import logger from 'redux-logger'
import adminReducer from './reducers'
import adminSaga from './sagas'
import { HashRouter } from 'react-router-dom'
import App from './app'
import { updateAuth } from './actions/auth'

const sagaMiddleware = createSagaMiddleware()
let middlewares = [sagaMiddleware]
if (process.env.NODE_ENV !== 'production') {
    middlewares.push(logger);
}
const store = createStore(adminReducer, applyMiddleware(...middlewares))
adminSaga.runSagas(sagaMiddleware)
store.dispatch(updateAuth(editPermission))

render((
    <Provider store={store}>
        <HashRouter>
            <App />
        </HashRouter>
    </Provider>
), document.getElementById('admin'))
