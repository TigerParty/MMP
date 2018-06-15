import React from 'react'
import debounce from 'lodash/debounce'
import PropTypes from 'prop-types'


class Search extends React.Component {
    constructor(props) {
        super(props)
        this.state= {
            value: props.value
        }
        this.handleChange = this.handleChange.bind(this)
        this.changed = debounce(this.props.handleValueChange, 300)
    }

    handleChange(e) {
        const val = e.target.value
        const { type } = this.props
        this.setState({ value: val }, () => {
            this.changed(type, val)
        })
    }

    componentWillReceiveProps(nextProps) {
        const { value } = nextProps
        const currentValue = this.state.value

        if(value!=currentValue) {
            this.setState({ value: value }, () => {
            return true
            })
        }
    }

    render() {
        const { value } = this.state

        return (
            <div className="input-group search">
                <div className="input-group-prepend d-lg-none">
                    <img src="../images/icon/search.svg" />
                </div>
                <input type="text"
                    className="form-control
                    border-0
                    rounded-0
                    py-2"
                    value={value}
                    onChange={this.handleChange}
                    placeholder="Search"/>
                <div className="input-group-append d-none d-lg-flex">
                    <img src="../images/icon/search.svg" />
                </div>
            </div>
        )
    }

}

Search.defaultProps = {
    value: ''
}

Search.propTypes = {
    value: PropTypes.string,
    type: PropTypes.string.isRequired,
    handleValueChange: PropTypes.func.isRequired
}

export default Search
