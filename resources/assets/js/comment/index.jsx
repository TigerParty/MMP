require('../bootstrap')
require('../../scss/app')
import 'babel-polyfill'
import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import { createStore, applyMiddleware } from 'redux'
import createSagaMiddleware from 'redux-saga'
import logger from 'redux-logger'
import commentApp from './reducers'
import commentSaga from './sagas'
import App from './app'
import { updateAuth } from './actions/auth'

const sagaMiddleware = createSagaMiddleware()
let middlewares = [sagaMiddleware]
if (process.env.NODE_ENV !== 'production') {
  middlewares.push(logger);
}

const store = createStore(commentApp, applyMiddleware(...middlewares))
commentSaga.runSagas(sagaMiddleware)
store.dispatch(updateAuth(editPermission))

render((
    <Provider store={store}>
        <App />
    </Provider>
), document.getElementById('commentApp'))
