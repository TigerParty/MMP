import React from 'react'


class Select extends React.Component {
    constructor(props) {
        super(props)
        this.handleChange = this.handleChange.bind(this)
    }

    handleChange(e) {
      const { type } = this.props
      const value = e.target.value
      this.props.valueChange(type, value)
    }

    render() {
        const { list, value, defaultOption } = this.props
        return (
            <select className="form-control
              rounded-0
              border-0
              py-2"
              value={value}
              onChange={this.handleChange}
              style={{ backgroundImage: "url(../images/icon/dropdown-triangle.svg)" }}
              disabled={list.length>0? false:true}>
              <option value="">{defaultOption}</option>
              { list.map(option => {
                return (
                  <option key={option.id} value={option.id}>{option.name || option.title}</option>
                )
              })}
            </select>
        )
    }

}

export default Select
