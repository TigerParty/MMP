import React from 'react'


class UserTypeField extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data } = this.props
        return (
          <div>
            <div className="d-inline-block
              rounded-circle
              mr-2
              dot"
              style={data ? {backgroundColor: data.color} : {backgroundColor: "#ffffff"}}></div>
            <span className="opacity-0-5">{data ? data.name:null}</span>
          </div>
        )
    }

}

export default UserTypeField
