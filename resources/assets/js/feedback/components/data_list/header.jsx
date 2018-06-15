import React from 'react'


class Header extends React.Component {
    constructor(props) {
        super(props)
    }

    handleOnClick(page) {
      const { pageChange } = this.props
      pageChange(page)
    }

    render() {
        const { order, from, to, current, total } = this.props
        return (
          <div className="row
            bg-yellow-tan
            pl-lg-4
            align-items-center
            text-greyish-brown
            header">
            <div className="col-auto
              ml-auto
              text-right
              opacity-0-5
              font-weight-bold">
              {from} – {to} of {total}
            </div>
            <div className="col-2
              px-0
              py-4
              pagination-buttons">
              <div className="d-flex">
                <button className="w-50 prev d-flex position-relative justify-content-center mute-button" disabled={from < 2} onClick={this.handleOnClick.bind(this, current - 1)}>
                  <img className="align-self-center" src="/images/icon/arrow-left.svg" />
                </button>
                <button className="w-50 next d-flex justify-content-center mute-button" disabled={!(total > to)} onClick={this.handleOnClick.bind(this, current + 1)}>
                  <img className="align-self-center" src="/images/icon/arrow-right.svg" />
                </button>
              </div>
            </div>
          </div>
        )
    }

}

export default Header
