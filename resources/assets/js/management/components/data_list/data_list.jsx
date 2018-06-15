import React from 'react'
import Item from './item/item'


class DataList extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data, permissionList, handleDelete, handleEdit, handleAdd, handleNotifySwitch} = this.props
        return (
            <div className="row data-list text-greyish-brown">
              <div className="col-12">
                <div className="table-responsive">
                  <table className="table">
                    <thead className="text-uppercase text-greyish-brown opacity-0-5 font-size-16">
                      <tr>
                        <th scope="col">username</th>
                        <th scope="col">email</th>
                        <th scope="col">user type</th>
                        <th scope="col">monthly <br/> reminder</th>
                        <th scope="col">{lang.management.user.fields.project}</th>
                        <th scope="col">NUMBER OF <br/>SUBMITTED REPORTS</th>
                        <th scope="col"></th>
                      </tr>
                    </thead>
                    <tbody>
                      {
                          data.map(item => <Item
                              {...item}
                              key={item.id}
                              userTypes={ permissionList }
                              editItem={ handleEdit }
                              deleteItem={ handleDelete }
                              handleAdd={ handleAdd }
                              toggleCheckbox={ handleNotifySwitch } />
                          )
                      }
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
        )
    }

}

export default DataList
