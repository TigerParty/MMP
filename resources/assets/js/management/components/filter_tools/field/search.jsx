import React from 'react'
import debounce from 'lodash/debounce'


class Search extends React.Component {
    constructor(props) {
        super(props)
        this.state= {
          value: props.value
        }
        this.handleChange = this.handleChange.bind(this)
        this.changed = debounce(this.props.valueChange, 300)
    }

    handleChange(e) {
      const val = e.target.value
      const { type } = this.props
      this.setState({ value: val }, () => {
        this.changed(type, val)
      })
    }

    componentWillReceiveProps(nextProps) {
      const { value } = nextProps
      const currentValue = this.state.value

      if(value!=currentValue) {
        this.setState({ value: value }, () => {
          return true
        })
      }
    }

    render() {
        const { value } = this.state

        return (
            <div className="input-group search">
              <input type="text"
                className="form-control
                  border-0
                  rounded-0
                  py-2"
                value={value}
                onChange={this.handleChange}
                placeholder="Search"/>
              <div className="input-group-append">
                <img src="../images/icon/search.svg" />
              </div>
            </div>
        )
    }

}

export default Search
