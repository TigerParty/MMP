import React from 'react'
import AccountInfo from './account_info'
import MainNavigationMobile from './main_navigation_mobile'
import MainNavigation from './main_navigation'

class Header extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            mobileMainNavAcitve: false
        }
        this.toggleMainnavigation = this.toggleMainnavigation.bind(this)
        this.checkIsLogin = this.checkIsLogin.bind(this)
    }

    toggleMainnavigation() {
        const mobileMainNavAcitve = this.state.mobileMainNavAcitve
        this.setState({
            mobileMainNavAcitve: !mobileMainNavAcitve
        })
    }

    checkIsLogin() {
        const { isLogin } = this.props
        const loginUrl = window.hasOwnProperty('constants') ? `${_.get(constants, 'BASE_URL', 'default')}/login` : ''
        if(isLogin) {
          return (
            <div className="col-auto
              align-self-center
              ml-auto
              font-size-18
              font-weight-bold
              text-dark-grey-blue
              text-uppercase
              d-none
              d-lg-block">
                <AccountInfo darkIcon={true}/>
            </div>)
        }

        return (
          <div className={`col-auto
            align-self-center
            ml-auto
            text-dark-grey-blue
            text-uppercase
            d-none
            d-lg-block
            login-btn`}>
            <a className="btn
              btn-primary
              text-uppercase
              w-100
              font-size-15
              py-2
              px-4"
              href={ loginUrl }>
              { lang.menu.login_btn }
            </a>
          </div>
        )
    }

    render() {
        const classMainNavAcitve = this.state.mobileMainNavAcitve ? 'open-man-nav' : ''
        const classCloseBtn = this.state.mobileMainNavAcitve ? '' : 'd-none'
        const classMenuBtn = this.state.mobileMainNavAcitve ? 'd-none' : ''
        const { isLogin } = this.props
        const { mobileMainNavAcitve } = this.state

        return (
          <header className={`container-fluid bg-white ${classMainNavAcitve}`}>
            <div className="row py-2 absolute-center-box">
              <div className="col-auto col-sm-auto col-lg-auto pr-2 align-self-center logo">
                <a href="/"><img className="img-fluid" src="/images/logo.png" /></a>
              </div>
              <div className="col-auto
                align-self-center
                pl-0
                font-size-28
                font-size-i7-30
                font-weight-bold
                text-dark-grey-blue
                logo">
                { lang.site.shorthead_title }
              </div>
              <MainNavigation />
              { this.checkIsLogin() }
              <div className={`col-auto
                align-self-center
                ml-auto
                font-size-18
                font-weight-bold
                text-dark-grey-blue
                text-uppercase
                d-lg-none
                ${classMenuBtn}`}
                onClick={ this.toggleMainnavigation }>
                { lang.menu.title }
              </div>
              <div className={`col-auto
                align-self-center
                ml-auto
                font-size-18
                font-weight-bold
                text-dark-grey-blue
                close-btn
                text-uppercase
                ${classCloseBtn}`}
                onClick={ this.toggleMainnavigation }>
                { lang.menu.close_btn }
              </div>
            </div>
            { mobileMainNavAcitve && <MainNavigationMobile isLogin= { isLogin }/> }
          </header>
        )
    }
}



export default Header
