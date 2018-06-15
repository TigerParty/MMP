import React from 'react'
import { Switch, Route, Redirect } from 'react-router-dom'
import AppRoute from './app_route'
import MainLayout from './master/main'
import Index from './containers/index'


const App = () => (
    <Switch>
        <AppRoute exact path="/" layout={ MainLayout } component={ Index } />
    </Switch>
)

export default App
