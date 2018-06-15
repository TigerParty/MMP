import React from 'react'
import { Link } from 'react-router-dom'
import UserType from '../field/user_type'
import CheckBox from '../field/checkbox'
import FunctionTools from '../field/function_tools'
import AddBtn from '../field/add_btn'
import { LAST_REPORT_DATETIME_FORMATE } from '../../../constants/index'


class ItemMobile extends React.Component {
    constructor(props) {
        super(props)
        this.handleOnClick = this.handleOnClick.bind(this)
        this.getUserType = this.getUserType.bind(this)
    }

    handleOnClick() {
      const { itemOnClick, id } = this.props
      itemOnClick(id)
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
            isActive,
            deleteItem,
            editItem,
            toggleCheckbox,
            handleAdd
          } = this.props
        const userType = this.getUserType()
        return (
            <div className={`row item ${isActive ? 'active':''}`}>
              <div className={`col-12`} >
                <div className="row
                  align-items-center
                  py-2
                  info-content"
                  onClick={ this.handleOnClick }>
                  <div className="col-10">
                    <div className="row">
                      <div className="col-12">
                        {name}
                      </div>
                      <div className="col-12 text-greyish-brown font-size-12">
                        <UserType data={userType} />
                      </div>
                    </div>
                  </div>
                  <div className="col-2 text-center arrow-block">
                    <img src="../images/icon/arrow-right.svg" />
                  </div>
                </div>
                <div className="row detail-content">
                  <div className="col-12 text-greyish-brown mb-3">
                    <div className="text-uppercase opacity-0-5">email:</div>
                    <div >{email}</div>
                  </div>
                  <div className="col-12 text-greyish-brown mb-3">
                    <div className="d-flex align-items-center">
                      <span className="text-uppercase opacity-0-5">Monthly Reminder</span>
                      <div className="ml-auto">
                        <CheckBox data={notification_enabled} id={id} editCheckbox={toggleCheckbox} />
                      </div>
                    </div>
                  </div>
                  <div className="col-12 text-greyish-brown mb-3">
                    <div className="text-uppercase opacity-0-5">issues:</div>
                    <div className="d-flex align-items-end">
                      <div className="pr-3">
                        <div>{`${report_count? report_count: 0} Reports Submitted`}</div>
                        {last_submitted_at ? <div>{`Last Submitted ${moment(last_submitted_at).format(LAST_REPORT_DATETIME_FORMATE)}`}</div>: null}
                      </div>
                      <AddBtn handleAdd={handleAdd} id={id} alignRight={true} />
                    </div>
                  </div>
                  <div className="col-12">
                    <FunctionTools id={id} deleteItem={deleteItem} editItem={editItem} />
                  </div>
                </div>
              </div>
            </div>
        )
    }

}

export default ItemMobile
