import React from 'react'
import PropTypes from 'prop-types'


class Select extends React.Component {
    constructor(props) {
        super(props)
        this.handleChange = this.handleChange.bind(this)
    }

    handleChange(e) {
      const { type, optionalKey } = this.props
      const value = e.target.value
      this.props.handleValueChange(type, value, optionalKey)
    }

    render() {
      const { list, value, defaultOption, valueKey, displayKey } = this.props
        return (
            <select className="form-control
              rounded-0
              border-0
              py-2
              select"
              value={value}
              onChange={this.handleChange}
              style={{ backgroundImage: "url(../images/icon/dropdown-triangle.svg)" }}
              disabled={list.length>0? false:true}>
              <option value="">{defaultOption}</option>
              { list.map((option, index) => {
                return (
                  <option key={index} value={valueKey ? option[valueKey] : option}>
                    { displayKey ? option[displayKey] : option }
                  </option>
                )
              })}
            </select>
        )
    }

}

Select.defaultProps = {
  list: [],
  value: '',
  defaultOption: 'select',
  valueKey: null,
  displayKey: null
}

Select.propTypes = {
  list: PropTypes.array,
  value: PropTypes.string,
  type: PropTypes.string.isRequired,
  defaultOption: PropTypes.string,
  handleValueChange: PropTypes.func.isRequired,
  valueKey: PropTypes.string,
  displayKey: PropTypes.string
}

export default Select
