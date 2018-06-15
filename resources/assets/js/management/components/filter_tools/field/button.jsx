import React from 'react'


class Button extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { handleOnClick, btnStr } = this.props
        return (
            <div className="
                font-size-14
                text-uppercase
                font-weight-bold
                clear-btn"
                onClick={handleOnClick}>
              {btnStr}
            </div>

        )
    }

}

export default Button
