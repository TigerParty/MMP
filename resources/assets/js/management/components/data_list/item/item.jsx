import React from 'react'
import { Link } from 'react-router-dom'
import UserType from '../field/user_type'
import CheckBox from '../field/checkbox'
import FunctionTools from '../field/function_tools'
import AddBtn from '../field/add_btn'
import { LAST_REPORT_DATETIME_FORMATE } from '../../../constants/index'


class Item extends React.Component {
    constructor(props) {
        super(props)
        this.getUserType = this.getUserType.bind(this)
    }

    getUserType() {
      const { userTypes, permission_level_id } = this.props
      const userType = _.find(userTypes, { 'id': permission_level_id })
      return userType
    }

    render() {
        const { id,
            name,
            email,
            notification_enabled,
            report_count,
            last_submitted_at,
            deleteItem,
            editItem,
            toggleCheckbox,
            handleAdd
          } = this.props
        const userType = this.getUserType()
        return (
            <tr className="item">
              <td>{name}</td>
              <td className="text-nowrap">{email}</td>
              <td className="info-content text-nowrap">
                <UserType data={userType} />
              </td>
              <td>
                <CheckBox id={id} data={notification_enabled} isDark={true} editCheckbox={toggleCheckbox} />
              </td>
              <td>
                <AddBtn handleAdd={handleAdd} id={id}/>
              </td>
              <td className="opacity-0-5 text-nowrap">
                  <div>{`${report_count? report_count: 0} Reports Submitted`}</div>
                  {last_submitted_at ? <div>{`Last Submitted ${moment(last_submitted_at).format(LAST_REPORT_DATETIME_FORMATE)}`}</div>: null}
              </td>
              <td>
                <FunctionTools id={id} deleteItem={deleteItem} editItem={editItem} />
              </td>
            </tr>
        )
    }

}

export default Item
