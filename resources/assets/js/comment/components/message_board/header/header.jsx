import React from 'react'


class Header extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { order, unit, total } = this.props

        return (
          <div className="row
            mx-0
            bg-primary
            align-items-center
            text-white
            position-relative
            detail-header">
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
