import React from 'react'
import swal from 'sweetalert'


class FunctionTools extends React.Component {
    constructor(props) {
        super(props)
        this.handleDelClick = this.handleDelClick.bind(this)
        this.handleEditClick = this.handleEditClick.bind(this)
    }

    handleDelClick() {
        const { id, deleteItem } = this.props
        swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover it!",
          icon: "warning",
          buttons: true,
          dangerMode: true
        })
        .then((willDelete) => {
          if (willDelete) {
            deleteItem(id)
          }
        })
    }

    handleEditClick() {
        const { editItem, id } = this.props
        editItem(id)
    }

    render() {
        const { editItem, deleteItem } = this.props
        return (
            <div className="row text-capitalize function-tools font-size-14 justify-content-end">
              {editItem &&
                <div className="col-6
                  d-flex
                  justify-content-center
                  align-items-center
                  py-2
                  cursor-pointer
                  edit-btn"
                  onClick={this.handleEditClick}>
                  <img src="../images/icon/create-black.svg"/>
                  <span className="pl-2">edit</span>
                </div>}
              {deleteItem &&
                <div className="col-6
                  d-flex
                  justify-content-center
                  align-items-center
                  py-2
                  cursor-pointer
                  delete-btn"
                  onClick={this.handleDelClick}>
                  <img src="../images/icon/delete-black.svg"/>
                  <span className="pl-2">delete</span>
                </div>}
            </div>
        )
    }

}

export default FunctionTools
