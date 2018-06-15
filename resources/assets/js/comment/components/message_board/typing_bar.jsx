import React from 'react'
import Avatar from '../../../components/avatar/avatar'



class TypingBar extends React.Component {
    constructor(props) {
        super(props)
        this.handleInputChange = this.handleInputChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
    }

    getInputValue() {
        return {
            name: this.refs.name.value,
            email: this.refs.email.value,
            message: this.refs.message.value,
        }
    }

    setInputValue(values) {
        this.refs.name.value = values.name
        this.refs.email.value = values.email
        this.refs.message.value = values.message
    }

    handleInputChange() {
        const { changeInput } = this.props
        changeInput(this.getInputValue())
    }

    handleSubmit(e) {
        e.preventDefault()
        const { id, type, commentSubmit } = this.props
        commentSubmit(id, type)
    }

    componentWillReceiveProps(nextProps) {
        const oldInputValue = this.props.newCommentInputs
        const { newCommentInputs } = nextProps
        if (newCommentInputs !== oldInputValue) {
            this.setInputValue(newCommentInputs)
        }
    }

    render() {
        const { newCommentInputs, sendBtnStr } = this.props

        return (
          <div className="col-12 mx-0 mb-2 typing-bar">
            <form className="row" onSubmit={ this.handleSubmit }>
              <div className="col-12">
                <div className="row py-3">
                  <div className="col-auto pr-0">
                    <svg fill="#B4B4B4" height="50" viewBox="0 0 24 24" width="50" xmlns="http://www.w3.org/2000/svg">
                      <path d="M0 0h24v24H0z" fill="none"/>
                      <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                    </svg>
                  </div>
                  <div className="col-4 align-self-center">
                    <input className="p-2"
                      type="text"
                      name="name"
                      ref="name"
                      onChange={ this.handleInputChange }
                      placeholder={ lang.feedback.comment.message_board.placeholders.name }
                    />
                  </div>
                  <div className="col-4 align-self-center">
                    <input className="p-2"
                      type="text"
                      name="email"
                      ref="email"
                      onChange={ this.handleInputChange }
                      placeholder={ lang.feedback.comment.message_board.placeholders.email }
                    />
                  </div>
                  <div className="col-10 clearfix mt-2">
                    <textarea className="p-2"
                      name="message"
                      ref="message"
                      onChange={ this.handleInputChange }
                      placeholder={ lang.feedback.comment.message_board.placeholders.typing_bar }
                    ></textarea>
                  </div>
                  <div className="col-2 align-self-center pl-0">
                    <button type="submit" className="w-100 border-0 font-weight-light">{ sendBtnStr }</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        )
    }

}

export default TypingBar
