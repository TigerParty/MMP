import React from 'react'
import Item from './item/item_mobile'


class DataListMobile extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            active_id: null,
        }

        this.handleOnClickItem = this.handleOnClickItem.bind(this)
    }

    handleOnClickItem(id) {
        if (this.state.active_id == id) {
            return this.setState({
                active_id: null
            })
        }
        this.setState({
            active_id: id
        })
    }

    render() {
        const { data, permissionList, handleDelete, handleEdit, handleAdd, handleNotifySwitch } = this.props
        return (
            <div className="row font-size-16 data-list">
                <div className="col-12">
                    {
                        data.map(item => <Item
                            {...item}
                            key={item.id}
                            itemOnClick={ this.handleOnClickItem }
                            isActive={ this.state.active_id==item.id }
                            userTypes={ permissionList }
                            editItem={ handleEdit }
                            deleteItem={ handleDelete }
                            handleAdd={ handleAdd }
                            toggleCheckbox={ handleNotifySwitch } />
                        )
                    }
                </div>
            </div>

        )
    }

}

export default DataListMobile
