import React from 'react'
import { Link } from 'react-router-dom'
import swal from 'sweetalert'


class HeaderMobile extends React.Component {
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
      const { date, from, goBackPath, editPermission } = this.props
        return (
          <div className="row
            py-3
            bg-primary
            align-items-center
            text-white
            detail-header-mobile
            absolute-center-box
            position-fixed">
            <div className="col-auto mr-auto">
              <Link to={ goBackPath }>
                <img src="/images/icon/page-back.svg" />
              </Link>
            </div>
            <div className="center-item center">
              <div className="d-flex flex-column text-center">
                <div className="text-white font-size-16">
                  { from }
                </div>
                <div className="text-white font-size-12 opacity-0-5">
                  { date }
                </div>
              </div>
            </div>
            { editPermission &&
              (<div className="col-auto ml-auto">
                <img src="/images/icon/delete.svg" className="cursor-pointer" onClick={this.handleDelete} />
              </div>)
            }
          </div>
        )
    }
}



export default HeaderMobile
