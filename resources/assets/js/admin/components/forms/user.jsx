import React from 'react'
import Input from './field/input'
import Select from './field/select'


class userForm extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            name: '',
            email: '',
            password: '',
            permission_level_id: ''
        }
        this.handleValueChange = this.handleValueChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
    }

    componentWillMount() {
        const { selectedUser } = this.props
        if(selectedUser) {
            this.handleValueChange('name', selectedUser.name)
            this.handleValueChange('email', selectedUser.email)
            this.handleValueChange('permission_level_id', selectedUser.permission_level_id)
        }
    }


    handleValueChange(key='name', val) {
        this.setState({
            [key]: val
        })
    }

    handleSubmit(e) {
        const { submitFrom, selectedUser } = this.props

        e.preventDefault
        if(selectedUser){
          return submitFrom(this.state, selectedUser.id)
        }
        submitFrom(this.state)
    }

    render() {
        const { name, email, password, permission_level_id } = this.state
        const { permissionList, formId, errors } = this.props
        const hasNameError = Object.prototype.hasOwnProperty.call(errors, 'name')
        const hasEmailError = Object.prototype.hasOwnProperty.call(errors, 'email')
        const hasPasswordError = Object.prototype.hasOwnProperty.call(errors, 'password')
        const hasUserTypeError = Object.prototype.hasOwnProperty.call(errors, 'permission_level_id')
        return (
            <form onSubmit={this.handleSubmit}
                  className="col-12 py-lg-4 col-lg-9 offset-lg-1"
                  id={formId}>
              <Input labelStr="name"
               inputId="name"
               error={errors.name}
               type="text"
               handleValueChange={this.handleValueChange}
               defaultValue={name}/>

              <Input labelStr="email"
               inputId="email"
               error={errors.email}
               type="email"
               handleValueChange={this.handleValueChange}
               defaultValue={email}/>

              <Input labelStr="password"
               inputId="password"
               error={errors.password}
               type="password"
               handleValueChange={this.handleValueChange}
               defaultValue={password}/>

              <Select labelStr="user type"
               inputId="permission_level_id"
               error={errors.permission_level_id}
               handleValueChange={this.handleValueChange}
               defaultValue={permission_level_id}
               list={permissionList}
               defaultOption="User Type"/>
            </form>

        )
    }

}

export default userForm
