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
            <form className="row p-3" onSubmit={ this.handleSubmit }>
              <div className="row">
                <div className="col-auto pr-0">
                  <img src="./images/icon/face.svg"/>
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
            </form>
          </div>
        )
    }

}

export default TypingBar
