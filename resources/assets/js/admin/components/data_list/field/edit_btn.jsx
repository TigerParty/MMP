import React from 'react'

class EditBtn extends React.Component {
    constructor(props) {
        super(props)
        this.handleClick = this.handleClick.bind(this)
    }

    handleClick() {
        const { handleEdit, id } = this.props
        handleEdit(id)
    }

    render() {
        return (
            <div className={`d-flex
                align-items-center
                cursor-pointer
                py-1
                pl-2
                pr-2`} onClick={this.handleClick}>
                <img src="../images/icon/create-black.svg" />
                <div className="text-capitalize ml-1">edit</div>
            </div>
        )
    }

}

export default EditBtn
