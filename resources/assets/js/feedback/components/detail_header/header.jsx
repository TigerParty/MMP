import React from 'react'
import swal from 'sweetalert'


class Header extends React.Component {
    constructor(props) {
        super(props)
        this.handleDelete = this.handleDelete.bind(this)
    }

    handleDelete() {
        const { id, deleteAction } = this.props
        swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover it!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            deleteAction(id)
          }
        })
    }

    render() {
        const { id, fromStr, date, from, editPermission ,deleteBtnStr, deleteAction } = this.props

        return (
          <div className="row
            mx-0
            bg-primary
            align-items-center
            text-white
            position-relative
            detail-header">
            {fromStr && (
              <div className="col-5
                col-xl-auto
                order-xl-1
                opacity-0-5
                font-size-16
                text-capitalize">
                { fromStr }:
              </div>
            )}
            <div className="col-5
              col-xl-auto
              order-xl-3
              ml-xl-auto
              pl-xl-0
              opacity-0-5
              text-right
              font-size-16">
              { date }
            </div>
            {from && (
              <div className="col-12
                col-xl-3
                text-nowrap
                order-xl-2
                font-size-20
                pl-xl-0">
                { from }
              </div>
            )}
            {editPermission &&
            (<div className="col-auto
              del-btn
              order-xl-4
              py-4
              text-white
              text-capitalize
              d-flex" onClick={ this.handleDelete }>
              <span>{ deleteBtnStr }</span>
              <img src="/images/icon/delete.svg"/>
            </div>)
            }
          </div>
        )
    }

}

export default Header
