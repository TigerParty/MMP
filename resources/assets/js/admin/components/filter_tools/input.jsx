import React from 'react'
import PropTypes from 'prop-types'


class Input extends React.Component {
    constructor(props) {
        super(props)
        this.handleChange = this.handleChange.bind(this)
    }

    handleChange(e) {
        const val = e.target.value
        const { handleValueChange, type, optionalKey } = this.props
        handleValueChange(type, val, optionalKey)
    }

    render() {
        const { value, inputType, PlaceholderStr } = this.props

        return (
            <div className="input-group search">
                <input type={inputType}
                    className="form-control
                    border-0
                    rounded-0
                    py-2"
                    value={value}
                    onChange={this.handleChange}
                    placeholder={PlaceholderStr}/>
            </div>
        )
    }

}

Input.defaultProps = {
    value: '',
    inputType: 'text',
    PlaceholderStr: 'Search'
}

Input.propTypes = {
    value: PropTypes.oneOfType([
        PropTypes.string,
        PropTypes.number
    ]),
    type: PropTypes.string.isRequired,
    inputType: PropTypes.string,
    handleValueChange: PropTypes.func.isRequired,
    optionalKey: PropTypes.oneOfType([
        PropTypes.string,
        PropTypes.number
    ]),
    PlaceholderStr: PropTypes.string
}

export default Input
