import React from 'react'
import { Link } from 'react-router-dom'


class ItemMobile extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { id, fields, isRead, linkTo } = this.props
        const readItemClass = isRead ? 'read' : ''
        return (
            <Link
              to={ linkTo }
              className={`row
              bg-white
              py-4
              px-lg-4
              mb-1
              align-items-end
              item
              cursor-pointer
              d-lg-none
              ${ readItemClass }`}>
            {
                fields.map((field, index) => {
                    return (
                        field
                    )
                })
            }
          </Link>
        )
    }

}

export default ItemMobile
