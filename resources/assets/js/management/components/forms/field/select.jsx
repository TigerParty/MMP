import React from 'react'


class Input extends React.Component {
    constructor(props) {
        super(props)
        this.handleOnChange = this.handleOnChange.bind(this)
    }

    handleOnChange(e) {
      const { inputId, handleValueChange } = this.props
      const val = e.target.value
      handleValueChange(inputId, val)
    }

    render() {
        const { labelStr, inputId, error, defaultValue, list, defaultOption } = this.props
        return (
            <div className="row form-group mt-4 align-items-center mb-lg-0">
              <label className={`col-12
               col-lg-3
               text-capitalize
               text-lg-right
               mb-lg-0
               ${error? 'mt-lg-4':null}`}
               htmlFor={inputId}>{labelStr}</label>
              <div className="col-12 col-lg-9">
                { error
                   && (<div className="invalid-feedback d-lg-block text-right">
                    { error.map((error, index) => {
                      return (
                          <div key="index">{error}</div>
                      )})
                    }
                    </div>
                  )
                }
                <select className={`form-control
                  rounded-0
                  py-2
                  custom
                  ${error? 'is-invalid':null}`}
                  id="userType"
                  defaultValue={defaultValue}
                  onChange={this.handleOnChange}
                  style={{ backgroundImage: "url(../images/icon/dropdown-triangle.svg)" }}>
                  <option value="" disabled hidden>{defaultOption}</option>
                  { list.map(option => {
                    return (
                      <option key={option.id} value={option.id}>{option.name}</option>
                    )
                  })}
                </select>
                <div className="invalid-feedback d-lg-none">
                  { error && error.map((error, index) => {
                    return (
                        <div key="index">{error}</div>
                    )})
                  }
                </div>
              </div>
            </div>
        )
    }

}

export default Input
