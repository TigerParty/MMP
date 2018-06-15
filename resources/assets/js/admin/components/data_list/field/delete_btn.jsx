import React from 'react'
import swal from 'sweetalert'


class DeletBtn extends React.Component {
    constructor(props) {
        super(props)
        this.handleClick = this.handleClick.bind(this)
    }

    handleClick() {
        const { handleDelete, id } = this.props
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover it!",
            icon: "warning",
            buttons: true,
            dangerMode: true
        })
        .then((willDelete) => {
            if (willDelete) {
                handleDelete(id)
            }
        })
    }

    render() {
        return (
            <div className={`d-flex
                align-items-center
                cursor-pointer
                py-1
                pl-2
                pr-2`} onClick={this.handleClick}>
                <img src="../images/icon/delete-black.svg" />
                <div className="text-capitalize ml-1">delete</div>
            </div>
        )
    }

}

export default DeletBtn
