import React from 'react'
import Avatar from '../../avatar/avatar'

class AccountInfo extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { darkIcon, isMobile } = this.props
        const baseUrl = window.hasOwnProperty('constants')? _.get(constants, 'BASE_URL', ''):''
        return (
            <div className={`account-info
                text-center
                text-uppercase
                font-size-15
                ${isMobile? 'mobile':''}`}>
                <div className="btn-group cursor-pointer">
                    <div className="d-flex align-items-center justify-content-center btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <Avatar/>
                    <div className="pl-3 pr-2 font-weight-bold">
                        {lang.menu.account_info.title}
                    </div>
                    <div className={`down-icon ${darkIcon? 'dark':''}`} >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fillRule="evenodd" d="M16.6 8.5L12 12.851 7.4 8.5 6 9.824l6 5.676 6-5.676z"/>
                        </svg>
                    </div>
                    </div>
                    <div className="dropdown-menu reround-0">
                        <a className="dropdown-item" href={`${baseUrl}/admin`}>{lang.site.nav_bar.admin_panel.name}</a>
                        <a className="dropdown-item" href={`${baseUrl}/admin/dync_form`}>{lang.site.nav_bar.admin_panel.form}</a>
                        <a className="dropdown-item" href={`${baseUrl}/admin/notification`}>{lang.site.nav_bar.admin_panel.notification}</a>
                        <a className="dropdown-item" href={`${baseUrl}/admin/status`}>{lang.site.nav_bar.admin_panel.status}</a>
                        <a className="dropdown-item" href={`${baseUrl}/management`}>{lang.site.nav_bar.admin_panel.user_management}</a>
                        <div className="dropdown-divider"></div>
                        <a className="dropdown-item" href={`${baseUrl}/logout`}>{lang.form.btn.logout }</a>
                    </div>
                </div>
            </div>
        )
    }
}



export default AccountInfo
