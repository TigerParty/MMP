import React from 'react'
import { connect } from 'react-redux'
import Header from '../../components/master/header/header'

class Main extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { children, isLogin } = this.props

        return (
            <div>
                <Header isLogin={ isLogin }>
                </Header>
                <div className="container-fluid app-content">
                    <div className="row">
                        <div className="col-12">
                            <main>
                                {children}
                            </main>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}


const mapStateToProps = state => {
    const { auth } = state
    return {
        isLogin: auth.isAdmin
    }
}

export default connect(mapStateToProps)(Main)

