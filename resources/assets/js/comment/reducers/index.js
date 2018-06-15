import { combineReducers } from 'redux'
import auth from './auth'
import comment from './comment'


const commentApp = combineReducers ({
    auth,
    comment,
})

export default commentApp
