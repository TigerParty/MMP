import React from 'react'


class Pagination extends React.Component {
    constructor(props) {
        super(props)
        this.handleOnClick = this.handleOnClick.bind(this)
    }

    handleOnClick(page) {
      const { pageChange, type } = this.props
      pageChange(type, page)
    }

    render() {
        const { pagination, currentPage } = this.props
        const { total, from, to } = pagination
        return (
          <div className="d-flex">
            <div className="
              text-center
              font-size-14
              font-weight-bold
              align-self-center
              text-greyish-brown
              opacity-0-5">
              {from} – {to} of {total}
            </div>
            <div className="align-items-center
              pagination-button ml-3 d-flex">
              <button className="btn bg-yellow-tan-light d-flex align-items-center" disabled={from<2} onClick={this.handleOnClick.bind(this, currentPage-1)}>
                <img className="mr-auto ml-auto" src="../images/icon/arrow-left.svg"/>
              </button>
              <button className="btn bg-yellow-tan-light d-flex align-items-center" disabled={!(total>to)} onClick={this.handleOnClick.bind(this, currentPage+1)}>
                <img className="mr-auto ml-auto" src="../images/icon/arrow-right.svg"/>
              </button>
            </div>
          </div>
        )
    }

}

export default Pagination
