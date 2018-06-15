import React from 'react'
import PropTypes from 'prop-types'

class Select extends React.Component {
    constructor(props) {
        super(props)
        this.handleChange = this.handleChange.bind(this)
    }

    handleChange(e) {
        const { handleValueChange } = this.props
        const value = e.target.value
        handleValueChange(value)
    }

    render() {
        const { list, value, defaultOption, blackTheme } = this.props
        return (
            <select className="rounded-0
                border-0
                py-2
                select"
                value={value}
                onChange={this.handleChange}
                style={{ backgroundImage: blackTheme ? "url(../images/icon/dropdown-triangle-black.svg)" : "url(../images/icon/dropdown-triangle-blue.svg)"}}
                disabled={list.length>0? false:true}>
                <option value="" disabled hidden>{defaultOption}</option>
                { list.map(option => {
                    return (
                        <option key={option.id} value={option.id}>{option.name || option.title}</option>
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
    blackTheme: false
}

Select.propTypes = {
    list: PropTypes.array,
    value: PropTypes.oneOfType([
        PropTypes.string,
        PropTypes.number
    ]),
    defaultOption: PropTypes.string,
    handleValueChange: PropTypes.func,
    blackTheme: PropTypes.bool
}

export default Select

