import React from 'react'


class CheckBoxField extends React.Component {
    constructor(props) {
        super(props)
        this.handleCheck = this.handleCheck.bind(this)
    }

    handleCheck(e) {
        const { editCheckbox, id } = this.props
        const val = e.target.checked
        editCheckbox(val, id)
    }

    render() {
        const { data, isDark } = this.props
        return (
          <label className="toggle-button">

            <input type="checkbox" className="position-absolute" checked={data} onChange={this.handleCheck} />
            <span className={`indicator ${isDark ? 'bg-light-grey':''}`}></span>
          </label>
        )
    }

}

export default CheckBoxField
