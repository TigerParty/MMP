import React from 'react'


class Empty extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { children } = this.props

        return (
          <div className="container-fluid">
            { children }
          </div>
        )
    }
}

export default Empty
