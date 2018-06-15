import React from 'react'
import { Link } from 'react-router-dom'


class Header extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { isMobile, order, unit, total, goBackPath } = this.props

        return (
          <div className="row
            mx-0
            bg-primary
            align-items-center
            text-white
            position-relative
            detail-header">
            {
              isMobile && (
                <div className="col-auto mr-auto">
                  <Link to={ goBackPath }>
                    <img src="/images/icon/page-back.svg" />
                  </Link>
                </div>
              )
            }
            <div className="col-auto
              font-size-16
              text-capitalize">
              { order }<img src="/images/icon/dropdown-triangle-white.svg"/>
            </div>
            <div className="col-auto
              ml-auto
              py-4
              text-capitalize
              d-flex" >
              { total } { unit }{ total > 1 && 's'}
            </div>
          </div>
        )
    }

}

export default Header
