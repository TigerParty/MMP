import React from 'react'


class TypingBar extends React.Component {
    constructor(props) {
        super(props)
        this.handleInputChange = this.handleInputChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
    }

    getInputValue() {
      return this.refs.inputValue.value
    }

    setInputValue(val) {
      this.refs.inputValue.value = val
    }

    handleInputChange(e) {
        const { changeInput } = this.props
        changeInput(this.getInputValue())
    }

    handleSubmit(e) {
        e.preventDefault()
        const { inputSubmit, id } = this.props
        inputSubmit(id)
   }

   componentWillReceiveProps(nextProps) {
        const oldInputValue = this.props.inputValue
        const { inputValue } = nextProps
        if (inputValue !== oldInputValue) {
            this.setInputValue(inputValue)
        }
    }

    render() {
        const { inputPlaceholder, sendBtnStr, inputValue, enableTypingBar } = this.props
        const disabledSubmitBtn = _.isEmpty(inputValue)
        return (
          <div className="row
            mx-0
            bg-white
            py-3
            align-items-center
            typing-bar">
            <form className="col-12" onSubmit={ this.handleSubmit }>
              <div className="row">
                <div className="col-9 pr-0">
                  <input className="
                    pl-0
                    form-control
                    form-control-lg
                    border-0
                    text-greyish-brown"
                    type="text"
                    name="inputValue"
                    ref="inputValue"
                    placeholder={ inputPlaceholder }
                    onChange={ this.handleInputChange } />
                </div>
                <div className="col-3 ml-auto">
                  <button
                    type="submit"
                    className="btn
                    send-btn
                    text-white
                    w-100
                    position-relative
                    px-lg-0
                    py-2
                    text-capitalize"
                    value={ inputValue }
                    disabled={ disabledSubmitBtn }
                    >
                    { sendBtnStr }
                  </button>
                </div>
              </div>
            </form>
          </div>
        )
    }

}

export default TypingBar
