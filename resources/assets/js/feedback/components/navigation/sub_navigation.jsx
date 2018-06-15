import React from 'react'
import { NavLink } from 'react-router-dom'


class SubNavigation extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { sms, app, voice, comment } = lang.menu.sub_navigation.feedback
        return (
          <nav className="
            row
            align-items-center
            text-white
            text-uppercase
            font-size-12
            font-size-lg-14
            sub-navigation">
            <NavLink className="col-3
              col-lg-12
              text-center
              text-lg-left
              item
              opacity-0-6
              py-2
              py-lg-4
              px-0
              pl-lg-4"
              activeClassName="acitve"
              to='/sms'>
              <img className="mb-2 mb-sm-1 mb-lg-0 mr-lg-3" src="/images/icon/sms.svg"/>
              { sms }
            </NavLink>
            <NavLink className="col-3
              col-lg-12
              text-center
              text-lg-left
              item
              opacity-0-6
              py-2
              py-lg-4
              px-0
              pl-lg-4"
              activeClassName="acitve"
              to='/report'>
              <img className="mb-2 mb-sm-1 mb-lg-0 mr-lg-3" src="/images/icon/app.svg"/>
              { app }
            </NavLink>
            {/* <NavLink className="col-3
              col-lg-12
              text-center
              text-lg-left
              item
              opacity-0-6
              py-2
              py-lg-4
              px-0
              pl-lg-4"
              activeClassName="acitve"
              to='/voice'>
              <img className="mb-2 mb-sm-1 mb-lg-0 mr-lg-3" src="/images/icon/voice.svg"/>
              { voice }
            </NavLink> */}
            <NavLink className="col-3
              col-lg-12
              text-center
              text-lg-left
              item
              opacity-0-6
              py-2
              py-lg-4
              px-0
              pl-lg-4"
              activeClassName="acitve"
              to='/comment'>
              <img className="mb-2 mb-sm-1 mb-lg-0 mr-lg-3" src="/images/icon/comments.svg"/>
              { comment }
            </NavLink>
          </nav>
        )
    }

}

export default SubNavigation
