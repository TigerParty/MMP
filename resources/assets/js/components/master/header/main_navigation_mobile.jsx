import React from 'react'
import { Link } from 'react-router-dom'
import AccountInfo from './account_info'

class MainNavigationMobile extends React.Component {
    constructor(props) {
        super(props)
        this.checkIsLogin = this.checkIsLogin.bind(this)
    }

    checkIsLogin() {
        const { isLogin } = this.props
        const loginUrl = window.hasOwnProperty('constants') ? `${_.get(constants, 'BASE_URL', '')}/login` : ''
        if(isLogin) {
          return (
            <div className="w-100
              fixed-bottom
              align-self-center
              ml-auto
              font-size-18
              font-weight-bold
              text-uppercase
              bg-primary
              text-white
              py-2">
              <AccountInfo isMobile={true} />
            </div>)
        }

        return (
          <div className="fixed-bottom">
            <a className="btn
              btn-primary
              text-uppercase
              w-100
              py-2
              px-5
              rounded-0"
              href={ loginUrl }>
              {lang.menu.login_btn}
            </a>
          </div>
        )
    }

    render() {
      const currentRootUrl = location.protocol + '//' + location.host + location.pathname
      const baseUrl = window.hasOwnProperty('constants')? _.get(constants, 'BASE_URL', ''):''
      const appUrl = window.hasOwnProperty('constants')? _.get(constants, 'PROAPP_URL', ''):''
        return (
          <nav className="font-weight-bold
            bg-white
            text-capitalize
            font-size-22
            main-navigation-mobile">
            <div className="d-flex
              flex-column
              text-center
              align-items-center
              content">
                <div className="item">
                <a className="text-greyish-brown" href={`${baseUrl}/explore`}>{lang.menu.main_navigation.search}</a>
                </div>
              <div className={`item pt-4 ${currentRootUrl == `${baseUrl}/feedback` ? 'active' : ''}` }>
                <a className="text-greyish-brown" href={`${baseUrl}/feedback`}>{lang.menu.main_navigation.feedback}</a>
                </div>
                <div className="item pt-4">
                <a className="text-greyish-brown" href={`${baseUrl}/tutorial`}>{lang.menu.main_navigation.tutorial}</a>
                </div>
                <div className="item pt-4">
                <a className="text-greyish-brown" href={appUrl}>{lang.menu.main_navigation.app}</a>
                </div>
            </div>
            { this.checkIsLogin() }
          </nav>
        )
    }

}

export default MainNavigationMobile
