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
        const { labelStr, inputId, error, type, defaultValue } = this.props
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
                <input type={type}
                  className={`form-control ${error? 'is-invalid':null}`}
                  id={inputId}
                  defaultValue={defaultValue}
                  onChange={this.handleOnChange} />
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
