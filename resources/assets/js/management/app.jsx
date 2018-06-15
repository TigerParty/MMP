import React from 'react'
import { Switch, Route, Redirect } from 'react-router-dom'
import AppRoute from './app_route'
import MainLayout from './master/main'
import UserIndex from './containers/user/index'


const App = () => (
    <Switch>
      <Route exact path="/" render={() => (<Redirect to="/user" />)}  />
      <AppRoute exact path="/user" layout={ MainLayout } component={ UserIndex } />
    </Switch>
)

export default App
