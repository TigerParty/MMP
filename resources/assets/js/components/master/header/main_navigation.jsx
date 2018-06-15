import React from 'react'
import AccountInfo from './account_info'
import { NavLink } from 'react-router-dom'


class MainNavigation extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const currentRootUrl = location.protocol + '//' + location.host + location.pathname
        const baseUrl = window.hasOwnProperty('constants')? _.get(constants, 'BASE_URL', ''):''
        const appUrl = window.hasOwnProperty('constants')? _.get(constants, 'PROAPP_URL', ''):''
        return (
          <div className="main-navigation d-none d-lg-block center-item center">
            <div className="row h-100">
              <a className={`col-auto
                align-self-center
                font-size-17
                font-weight-bold
                text-capitalize
                item
                opacity-0-5
                letter-spacing-0-4`}
                href={`${baseUrl}/explore`}>
                <span>{lang.menu.main_navigation.search}</span>
              </a>
              <a className={`col-auto
                align-self-center
                font-size-17
                font-weight-bold
                text-capitalize
                item
                opacity-0-5
                letter-spacing-0-4
                ${currentRootUrl == `${baseUrl}/feedback`? 'active':''}`}
                href={`${baseUrl}/feedback`}>
                <span>{lang.menu.main_navigation.feedback}</span>
              </a>
              <a className={`col-auto
                align-self-center
                font-size-17
                font-weight-bold
                text-capitalize
                item
                opacity-0-5
                letter-spacing-0-4`}
                href={`${baseUrl}/tutorial`}>
                <span>{lang.menu.main_navigation.tutorial}</span>
              </a>
              <a className={`col-auto
                align-self-center
                font-size-17
                font-weight-bold
                text-capitalize
                item
                opacity-0-5
                letter-spacing-0-4`}
                href={appUrl}>
                <span>{lang.menu.main_navigation.app}</span>
              </a>
            </div>
          </div>
        )
    }

}

export default MainNavigation
