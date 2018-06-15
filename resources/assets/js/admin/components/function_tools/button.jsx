import React from 'react'
import PropTypes from 'prop-types'

class Button extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { title, handleClick, passValue, customClass, buttonHeight } = this.props

        return (
            <div className={`cursor-pointer d-flex align-items-center justify-content-center ${customClass}`}
                style={{ height: buttonHeight}}
                onClick={() => { handleClick(passValue) }}>
                {title}
            </div>
        )
    }

}

Button.defaultProps = {
    title: '',
    passValue: null,
    customClass: null,
    buttonHeight: '45px'
}

Button.propTypes = {
    title: PropTypes.string.isRequired,
    handleClick: PropTypes.func.isRequired,
    passValue: PropTypes.oneOfType([
        PropTypes.string,
        PropTypes.number
    ]),
    customClass: PropTypes.string,
    buttonHeight: PropTypes.string
}


export default Button
