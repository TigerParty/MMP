import React from 'react'
import PropTypes from 'prop-types'

class Counter extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { value, subTitle, needdividerLine } = this.props

        return (
            <div className={`d-flex
                flex-column
                text-greyish-brown
                position-relative
                counter
                ${needdividerLine ? 'divider-line pr-3' : 'pl-3'}`} >
                <div className="font-weight-light
                    font-size-30
                    font-size-md-35
                    font-size-lg-40
                    line-height-40">{value}</div>
                <div className="opacity-0-5
                    font-size-8
                    font-size-md-12
                    line-height-12">{subTitle}</div>
            </div>
        )
    }

}

Counter.defaultProps = {
    value: 0,
    subTitle: '',
    needdividerLine: false
}

Counter.propTypes = {
    value: PropTypes.number,
    subTitle: PropTypes.string,
    needdividerLine: PropTypes.bool
}

export default Counter
