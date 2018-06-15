import React from 'react'
import { Switch, Route, Redirect } from 'react-router-dom'
import AppRoute from './app_route'
import MainLayout from './master/main'
import EmptyLayout from './master/empty'
import SMSIndex from './containers/sms/index'
import SMSShow from './containers/sms/show'
import ReportIndex from './containers/report/index'
import ReportShow from './containers/report/show'
import VoiceIndex from './containers/voice/index'
import VoiceShow from './containers/voice/show'
import CommentIndex from './containers/comment/index'
import CommentShow from './containers/comment/show'


const App = () => (
    <Switch>
      <Route exact path="/" render={() => (<Redirect to="/sms" />)}  />
      <AppRoute exact path="/sms" layout={ MainLayout } component={ SMSIndex } />
      <AppRoute path="/sms/:id" layout={ EmptyLayout } component={ SMSShow } />
      <AppRoute exact path="/report" layout={ MainLayout } component={ ReportIndex } />
      <AppRoute path="/report/:id" layout={ EmptyLayout } component={ ReportShow } />
      <AppRoute exact path="/voice" layout={ MainLayout } component={ VoiceIndex } />
      <AppRoute path="/voice/:id" layout={ EmptyLayout } component={ VoiceShow } />
      <AppRoute exact path="/comment" layout={ MainLayout } component={ CommentIndex } />
      <AppRoute path="/comment/:id" layout={ EmptyLayout } component={ CommentShow } />
    </Switch>
)

export default App
