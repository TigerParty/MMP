import React from 'react'
import swal from 'sweetalert'


class addBtn extends React.Component {
    constructor(props) {
        super(props)
        this.handleClick = this.handleClick.bind(this)
    }

    handleClick() {
        const { handleAdd, id } = this.props
        handleAdd(id)
    }

    render() {
        const { alignRight } = this.props
        return (
            <div className={`d-flex align-items-center cursor-pointer ${alignRight? 'ml-auto':null}`} onClick={this.handleClick}>
              <img src="../images/icon/add-circle.svg"/>
              <div className="text-capitalize text-primary ml-1">add</div>
            </div>
        )
    }

}

export default addBtn
