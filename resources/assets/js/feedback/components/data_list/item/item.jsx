import React from 'react'
import { Link } from 'react-router-dom'


class Item extends React.Component {
    constructor(props) {
        super(props)
        this.handleClick = this.handleClick.bind(this)
    }

    handleClick() {
      const { onClickItem, isSelected, id, updatedStatus, isRead, isAdmin } = this.props
      if(isSelected){
        return
      } else {
        onClickItem(id)
      }

      if(isAdmin && !isRead && updatedStatus) {
        updatedStatus(id)
      }
    }

    render() {
        const { fields, isRead, isSelected } = this.props
        const readItemClass = isRead ? 'read' : ''
        const selectedItemClass = isSelected ? 'active' : ''
        return (
            <div className={`row
              bg-white
              py-4
              px-lg-4
              mb-1
              align-items-end
              item
              cursor-pointer
              d-none
              d-lg-flex
              ${ selectedItemClass }
              ${ readItemClass }`}
              onClick={ this.handleClick }>
              {
                  fields.map((field, index) => {
                      return (
                          field
                      )
                  })
              }

            </div>
        )
    }

}

export default Item
