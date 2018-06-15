import { combineReducers } from 'redux'
import auth from './auth'
import admin from './admin'
import region from './region'


const adminApp = combineReducers ({
    auth,
    admin,
    region
})

export default adminApp
