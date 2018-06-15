require('../bootstrap')
require('../../scss/app')
require('../../scss/management/management')
import 'babel-polyfill'
import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import { createStore, applyMiddleware } from 'redux'
import createSagaMiddleware from 'redux-saga'
import logger from 'redux-logger'
import managementReducer from './reducers'
import managementSaga from './sagas'
import { HashRouter } from 'react-router-dom'
import App from './app'
import { updateAuth } from './actions/auth'

const sagaMiddleware = createSagaMiddleware()
let middlewares = [sagaMiddleware]
if (process.env.NODE_ENV !== 'production') {
  middlewares.push(logger);
}
const store = createStore(managementReducer, applyMiddleware(...middlewares))
managementSaga.runSagas(sagaMiddleware)
store.dispatch(updateAuth(editPermission))

render((
    <Provider store={store}>
        <HashRouter>
            <App />
        </HashRouter>
    </Provider>
), document.getElementById('management'))
